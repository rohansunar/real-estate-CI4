<?php

namespace App\Models;

use CodeIgniter\Model;
use Throwable;

/**
 * MemberModel (MLM)
 *
 * Wraps the existing agents table for MLM member operations and adds
 * high-performance recursive CTE helpers for tree traversal and level analytics.
 *
 * Table: agents
 * Key fields: id, name, email, is_active, parent_agent_id, created_at, unique_agent_id
 */
class MemberModel extends AgentModel
{
    /**
     * Fetch a limited-depth subtree starting at $memberId using MySQL recursive CTE.
     * Returns a normalized nested array suitable for JSON output and Treant/OrgChart.
     *
     * @param int $memberId Root member id
     * @param int $depth    Max depth to include relative to root (>=1)
     */
    public function getTreeCTE(int $memberId, int $depth = 3): array
    {
        $depth = max(1, $depth);

        $sql = <<<SQL
        WITH RECURSIVE tree AS (
            SELECT a.id,
                   a.name,
                   a.email,
                   a.is_active,
                   a.parent_agent_id,
                   a.created_at,
                   a.unique_agent_id,
                   0 AS level
            FROM agents a
            WHERE a.id = :root:

            UNION ALL

            SELECT c.id,
                   c.name,
                   c.email,
                   c.is_active,
                   c.parent_agent_id,
                   c.created_at,
                   c.unique_agent_id,
                   t.level + 1 AS level
            FROM agents c
            JOIN tree t ON c.parent_agent_id = t.id
            WHERE t.level < :maxDepth:
        )
        SELECT * FROM tree ORDER BY level, name;
        SQL;

        $db = $this->db;
        $result = $db->query($sql, [
            'root' => $memberId,
            'maxDepth' => $depth - 1, // children levels beyond root
        ])->getResultArray();

        if (!$result) {
            return [];
        }

        // Preload a map of direct child counts to mark has_children efficiently
        $ids = array_column($result, 'id');
        $childCounts = [];
        if (!empty($ids)) {
            $in = implode(',', array_map('intval', $ids));
            $ccSql = "SELECT parent_agent_id AS pid, COUNT(*) AS cnt FROM agents WHERE parent_agent_id IN ($in) GROUP BY parent_agent_id";
            $rows = $db->query($ccSql)->getResultArray();
            foreach ($rows as $r) {
                $childCounts[(int) $r['pid']] = (int) $r['cnt'];
            }
        }

        // Build adjacency list
        $byParent = [];
        $byId = [];
        foreach ($result as $row) {
            $id = (int) $row['id'];
            $pid = $row['parent_agent_id'] !== null ? (int) $row['parent_agent_id'] : null;
            $byParent[$pid][] = $id;
            $byId[$id] = [
                'id' => $id,
                'name' => $row['name'],
                'unique_id' => $row['unique_agent_id'],
                'level' => (int) $row['level'],
                'join_date' => $row['created_at'] ? date('m/d/Y', strtotime($row['created_at'])) : null,
                'is_active' => (bool) $row['is_active'],
                'has_children' => ($childCounts[$id] ?? 0) > 0,
                'children' => [],
            ];
        }

        // Build nested tree
        $root = $memberId;
        $stack = $byParent[$byId[$root]['parent_agent_id'] ?? null] ?? [$root];
        // Ensure $root exists
        if (!isset($byId[$root])) {
            return [];
        }

        $build = function ($id) use (&$build, &$byParent, &$byId, $depth) {
            $node = $byId[$id];
            if ($node['level'] + 1 >= $depth) {
                // Do not attach further children beyond depth
                return $node;
            }
            foreach ($byParent[$id] ?? [] as $childId) {
                $node['children'][] = $build($childId);
            }
            return $node;
        };

        return $build($root);
    }

    /**
     * Level summary analytics starting from all top-level roots (parent NULL or 0),
     * or from a specific root when $memberId provided.
     * Returns rows: level, total_count, active_count, active_percent
     */
    public function getLevelsSummaryCTE(?int $memberId = null, int $page = 1, int $limit = 20, string $orderBy = 'level', string $orderDir = 'ASC'): array
    {
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));
        $offset = ($page - 1) * $limit;

        $db = $this->db;
        if ($memberId) {
            $anchorClause = 'WHERE a.id = :root:';
            $params = ['root' => $memberId];
        } else {
            $anchorClause = 'WHERE (a.parent_agent_id IS NULL OR a.parent_agent_id = 0)';
            $params = [];
        }

        $sql = <<<SQL
        WITH RECURSIVE tree AS (
            SELECT a.id, a.parent_agent_id, a.is_active, 0 AS level
            FROM agents a
            $anchorClause
            UNION ALL
            SELECT c.id, c.parent_agent_id, c.is_active, t.level + 1
            FROM agents c
            JOIN tree t ON c.parent_agent_id = t.id
        )
        SELECT level,
               COUNT(*) AS total_count,
               SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_count,
               ROUND((SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(*),0)) * 100, 2) AS active_percent
        FROM tree
        GROUP BY level
        ORDER BY {$orderBy} {$orderDir}
        LIMIT :limit: OFFSET :offset:
        SQL;

        $rows = $db->query($sql, array_merge($params, [
            'limit' => $limit,
            'offset' => $offset,
        ]))->getResultArray();

        // total levels for pagination
        $countSql = <<<SQL
        WITH RECURSIVE tree AS (
            SELECT a.id, a.parent_agent_id, 0 AS level
            FROM agents a
            $anchorClause
            UNION ALL
            SELECT c.id, c.parent_agent_id, t.level + 1
            FROM agents c
            JOIN tree t ON c.parent_agent_id = t.id
        )
        SELECT COUNT(DISTINCT level) AS levels
        FROM tree
        SQL;

        $totalLevels = (int) ($db->query($countSql, $params)->getFirstRow('array')['levels'] ?? 0);

        return [
            'data' => $rows,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total_levels' => $totalLevels,
                'total_pages' => $limit > 0 ? (int) ceil($totalLevels / $limit) : 1,
            ],
        ];
    }

    /** Search members by name (LIKE) or unique_agent_id (exact) */
    public function searchMembers(string $term, string $type = 'name', int $limit = 10): array
    {
        $builder = $this->builder();
        if ($type === 'id') {
            $builder->where('unique_agent_id', $term);
        } else {
            $builder->like('name', $term, 'both', true); // case-insensitive
        }
        return $builder->orderBy('created_at', 'DESC')->limit($limit)->get()->getResultArray();
    }

    /** Get full upline path to root for navigation breadcrumb */
    public function getUplinePathCTE(int $memberId): array
    {
        $sql = <<<SQL
        WITH RECURSIVE up AS (
           SELECT a.id, a.parent_agent_id, a.name, a.unique_agent_id, 0 AS level
           FROM agents a WHERE a.id = :id:
           UNION ALL
           SELECT p.id, p.parent_agent_id, p.name, p.unique_agent_id, up.level + 1
           FROM agents p
           JOIN up ON up.parent_agent_id = p.id
        )
        SELECT * FROM up;
        SQL;
        $rows = $this->db->query($sql, ['id' => $memberId])->getResultArray();
        // Return from root down to member
        usort($rows, function ($a, $b) { return $b['level'] <=> $a['level']; });
        return $rows;
    }

    /** Fetch minimal member details */
    public function getMemberDetails(int $memberId): ?array
    {
        $row = $this->select(['id','name','email','phone','address','qualification','is_active','unique_agent_id','parent_agent_id','created_at'])
                    ->find($memberId);
        return $row ?: null;
    }
}


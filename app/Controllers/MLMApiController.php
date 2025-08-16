<?php

namespace App\Controllers;

use App\Models\MemberModel;
use CodeIgniter\HTTP\ResponseInterface;

class MLMApiController extends BaseController
{
    protected MemberModel $members;

    public function __construct()
    {
        $this->members = new MemberModel();
    }

    /**
     * GET /mlm/tree/{memberId}?depth=3
     */
    public function tree(int $memberId)
    {
        $depth = (int) ($this->request->getGet('depth') ?? 3);
        $depth = max(1, min(10, $depth));
        try {
            $data = $this->members->getTreeCTE($memberId, $depth);
            if (empty($data)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                                       ->setJSON(['status' => 'error', 'message' => 'Member not found or no data']);
            }
            return $this->response->setJSON(['status' => 'ok', 'data' => $data]);
        } catch (\Throwable $e) {
            log_message('error', 'MLM tree error: {msg}', ['msg' => $e->getMessage()]);
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                                   ->setJSON(['status' => 'error', 'message' => 'Failed to fetch tree']);
        }
    }

    /**
     * GET /mlm/levels-summary?page=1&limit=20&memberId=
     */
    public function levelsSummary()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = (int) ($this->request->getGet('limit') ?? 20);
        $memberId = $this->request->getGet('memberId');
        $memberId = $memberId !== null ? (int) $memberId : null;
        $orderBy = $this->request->getGet('orderBy') ?? 'level';
        $orderDir = strtoupper($this->request->getGet('orderDir') ?? 'ASC');
        $orderDir = $orderDir === 'DESC' ? 'DESC' : 'ASC';
        try {
            $result = $this->members->getLevelsSummaryCTE($memberId, $page, $limit, $orderBy, $orderDir);
            return $this->response->setJSON(['status' => 'ok'] + $result);
        } catch (\Throwable $e) {
            log_message('error', 'MLM levelsSummary error: {msg}', ['msg' => $e->getMessage()]);
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                                   ->setJSON(['status' => 'error', 'message' => 'Failed to fetch level summary']);
        }
    }

    /** CSV export for levels summary: GET /mlm/levels-summary.csv?memberId= */
    public function levelsSummaryCsv()
    {
        $memberId = $this->request->getGet('memberId');
        $memberId = $memberId !== null ? (int) $memberId : null;
        try {
            $result = $this->members->getLevelsSummaryCTE($memberId, 1, 1000);
            $rows = $result['data'] ?? [];
            $csv = fopen('php://temp', 'r+');
            fputcsv($csv, ['Level', 'Total Members', 'Active Members', 'Active %']);
            foreach ($rows as $r) {
                fputcsv($csv, [$r['level'], $r['total_count'], $r['active_count'], $r['active_percent']]);
            }
            rewind($csv);
            $content = stream_get_contents($csv);
            fclose($csv);
            return $this->response
                ->setHeader('Content-Type', 'text/csv')
                ->setHeader('Content-Disposition', 'attachment; filename="levels_summary.csv"')
                ->setBody($content);
        } catch (\Throwable $e) {
            log_message('error', 'MLM CSV export error: {msg}', ['msg' => $e->getMessage()]);
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                                   ->setBody('Failed to export CSV');
        }
    }

    /**
     * GET /mlm/search?query=...&type=name|id
     */
    public function search()
    {
        $term = trim((string) ($this->request->getGet('query') ?? ''));
        $type = $this->request->getGet('type') === 'id' ? 'id' : 'name';
        if ($term === '') {
            return $this->response->setJSON(['status' => 'ok', 'results' => []]);
        }
        try {
            $results = $this->members->searchMembers($term, $type, 10);
            // Augment first result with upline path to root for navigation
            if (!empty($results)) {
                $path = $this->members->getUplinePathCTE((int) $results[0]['id']);
            } else {
                $path = [];
            }
            return $this->response->setJSON([
                'status' => 'ok',
                'results' => $results,
                'path' => $path,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'MLM search error: {msg}', ['msg' => $e->getMessage()]);
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                                   ->setJSON(['status' => 'error', 'message' => 'Search failed']);
        }
    }

    /**
     * GET /mlm/member/{memberId}/details
     */
    public function memberDetails(int $memberId)
    {
        try {
            $row = $this->members->getMemberDetails($memberId);
            if (!$row) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                                       ->setJSON(['status' => 'error', 'message' => 'Member not found']);
            }
            return $this->response->setJSON(['status' => 'ok', 'data' => $row]);
        } catch (\Throwable $e) {
            log_message('error', 'MLM memberDetails error: {msg}', ['msg' => $e->getMessage()]);
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                                   ->setJSON(['status' => 'error', 'message' => 'Failed to fetch member details']);
        }
    }
}


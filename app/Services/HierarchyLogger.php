<?php

namespace App\Services;

/**
 * HierarchyLogger
 *
 * Lightweight file-based logger dedicated to hierarchy operations.
 * - Writes to writable/logs/hierarchy-YYYY-MM-DD.log
 * - Simple, dependency-free, safe in shared hosting environments
 *
 * Usage:
 *  $logger = new \App\Services\HierarchyLogger();
 *  $logger->log('access', 'Admin viewed agents hierarchy', ['user_id' => 1]);
 */
class HierarchyLogger
{
    private string $logDir;

    public function __construct()
    {
        $this->logDir = WRITEPATH . 'logs';
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0775, true);
        }
    }

    /**
     * Write a log entry
     *
     * @param string $action Short action key, e.g., 'access', 'expand_node', 'view_logs'
     * @param string $message Human-readable message
     * @param array  $context Arbitrary metadata (agent_id, user_id, ip, etc.)
     */
    public function log(string $action, string $message, array $context = []): void
    {
        try {
            $timestamp = date('Y-m-d H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
            $line = [
                'time' => $timestamp,
                'ip' => $ip,
                'action' => $action,
                'message' => $message,
                'context' => $context,
            ];
            $logFile = $this->logDir . '/hierarchy-' . date('Y-m-d') . '.log';
            @file_put_contents($logFile, json_encode($line) . PHP_EOL, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            // Fall back to CodeIgniter logger without interrupting user flow
            log_message('error', 'HierarchyLogger failure: ' . $e->getMessage());
        }
    }

    /**
     * Get recent log lines filtered by agent ID (best-effort; returns strings)
     *
     * @param int $agentId
     * @param int $limit Max number of lines to return
     * @return array<string>
     */
    public function getRecentByAgent(int $agentId, int $limit = 50): array
    {
        $logFile = $this->logDir . '/hierarchy-' . date('Y-m-d') . '.log';
        if (!is_file($logFile)) {
            return [];
        }

        $lines = @file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $results = [];

        // Scan in reverse for most-recent-first
        for ($i = count($lines) - 1; $i >= 0 && count($results) < $limit; $i--) {
            $raw = $lines[$i];
            $decoded = json_decode($raw, true);
            if (!is_array($decoded)) {
                continue;
            }
            $ctx = $decoded['context'] ?? [];
            if (isset($ctx['agent_id']) && (int) $ctx['agent_id'] === $agentId) {
                $results[] = sprintf(
                    '[%s] %s — %s',
                    $decoded['time'] ?? '-',
                    strtoupper($decoded['action'] ?? 'LOG'),
                    $decoded['message'] ?? ''
                );
            }
        }

        return $results;
    }
}


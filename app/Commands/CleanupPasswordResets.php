<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PasswordResetModel;

/**
 * CleanupPasswordResets Command
 *
 * This command cleans up expired and used password reset tokens from the database.
 * It should be run periodically (e.g., via cron job) to maintain database cleanliness
 * and prevent accumulation of old tokens.
 *
 * Usage:
 * php spark cleanup:password-resets
 *
 * Features:
 * - Removes expired tokens (older than 1 hour)
 * - Removes used tokens
 * - Provides detailed output of cleanup results
 * - Safe to run multiple times
 *
 * @author Real Estate Team
 * @version 1.0
 * @since 2025-08-04
 */
class CleanupPasswordResets extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Maintenance';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'cleanup:password-resets';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Clean up expired and used password reset tokens';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'cleanup:password-resets';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--dry-run' => 'Show what would be cleaned up without actually deleting',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $passwordResetModel = new PasswordResetModel();
        $isDryRun = CLI::getOption('dry-run');

        CLI::write('Password Reset Token Cleanup', 'yellow');
        CLI::write('================================', 'yellow');
        CLI::newLine();

        try {
            if ($isDryRun) {
                CLI::write('DRY RUN MODE - No tokens will be deleted', 'cyan');
                CLI::newLine();

                // Count tokens that would be deleted
                $expiredCount = $passwordResetModel->where('expires_at <', date('Y-m-d H:i:s'))->countAllResults();
                $usedCount = $passwordResetModel->where('used', 1)->countAllResults();
                $totalCount = $expiredCount + $usedCount;

                CLI::write("Expired tokens that would be deleted: {$expiredCount}", 'white');
                CLI::write("Used tokens that would be deleted: {$usedCount}", 'white');
                CLI::write("Total tokens that would be deleted: {$totalCount}", 'white');
            } else {
                // Perform actual cleanup
                $deletedCount = $passwordResetModel->cleanupExpiredTokens();

                if ($deletedCount > 0) {
                    CLI::write("Successfully cleaned up {$deletedCount} password reset tokens.", 'green');
                } else {
                    CLI::write('No expired or used tokens found to clean up.', 'blue');
                }
            }

            // Show current statistics
            CLI::newLine();
            $activeTokens = $passwordResetModel->where('expires_at >', date('Y-m-d H:i:s'))
                                              ->where('used', 0)
                                              ->countAllResults();
            CLI::write("Active tokens remaining: {$activeTokens}", 'cyan');

        } catch (\Exception $e) {
            CLI::error('Error during cleanup: ' . $e->getMessage());
            return EXIT_ERROR;
        }

        CLI::newLine();
        CLI::write('Cleanup completed successfully!', 'green');
        return EXIT_SUCCESS;
    }
}

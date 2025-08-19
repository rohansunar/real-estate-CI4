<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PasswordResetModel
 *
 * Handles password reset token management for secure password recovery.
 * This model manages:
 * - Creating secure password reset tokens
 * - Validating tokens and expiration times
 * - Cleaning up expired and used tokens
 * - Email-based password recovery workflow
 *
 * Security Features:
 * - Cryptographically secure token generation
 * - Token expiration (default 1 hour)
 * - One-time use tokens
 * - Automatic cleanup of expired tokens
 *
 * @author White Rock Realtor Team
 * @version 1.0
 * @since 2025-08-04
 */
class PasswordResetModel extends Model
{
    protected $table            = 'password_reset_tokens';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['email', 'token', 'expires_at', 'used'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Create a new password reset token
     *
     * @param string $email User's email address
     * @return string|false Generated token or false on failure
     */
    public function createResetToken(string $email)
    {
        // Clean up any existing tokens for this email
        $this->where('email', $email)->delete();

        // Generate secure token
        $token = bin2hex(random_bytes(32));
        
        // Set expiration time (1 hour from now)
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $data = [
            'email' => $email,
            'token' => hash('sha256', $token), // Store hashed version
            'expires_at' => $expiresAt,
            'used' => 0
        ];

        if ($this->insert($data)) {
            return $token; // Return unhashed token for email
        }

        return false;
    }

    /**
     * Validate a password reset token
     *
     * @param string $token The token to validate
     * @param string $email The email address
     * @return bool True if token is valid
     */
    public function validateToken(string $token, string $email): bool
    {
        $hashedToken = hash('sha256', $token);
        
        $resetToken = $this->where('email', $email)
                          ->where('token', $hashedToken)
                          ->where('used', 0)
                          ->where('expires_at >', date('Y-m-d H:i:s'))
                          ->first();

        return $resetToken !== null;
    }

    /**
     * Mark a token as used
     *
     * @param string $token The token to mark as used
     * @param string $email The email address
     * @return bool True if successful
     */
    public function markTokenAsUsed(string $token, string $email): bool
    {
        $hashedToken = hash('sha256', $token);
        
        return $this->where('email', $email)
                   ->where('token', $hashedToken)
                   ->set(['used' => 1])
                   ->update();
    }

    /**
     * Clean up expired and used tokens
     * Should be called periodically to maintain database cleanliness
     *
     * @return int Number of tokens cleaned up
     */
    public function cleanupExpiredTokens(): int
    {
        $deletedCount = $this->where('expires_at <', date('Y-m-d H:i:s'))
                            ->orWhere('used', 1)
                            ->countAllResults();
        
        $this->where('expires_at <', date('Y-m-d H:i:s'))
            ->orWhere('used', 1)
            ->delete();

        return $deletedCount;
    }

    /**
     * Get token details for validation
     *
     * @param string $token The token
     * @param string $email The email
     * @return array|null Token details or null if not found
     */
    public function getTokenDetails(string $token, string $email): ?array
    {
        $hashedToken = hash('sha256', $token);
        
        return $this->where('email', $email)
                   ->where('token', $hashedToken)
                   ->first();
    }
}

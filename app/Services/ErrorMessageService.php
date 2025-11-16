<?php

namespace App\Services;

/**
 * ErrorMessageService
 * 
 * Centralized service for generating user-friendly error messages.
 * This service provides consistent, user-friendly error messages across
 * the entire application while maintaining technical error logging.
 * 
 * Key Features:
 * - Consistent error message formatting
 * - Context-aware error messages
 * - Fallback messages for unknown errors
 * - Support for different error types (validation, database, file upload, etc.)
 * - Localization support ready
 * 
 * @author Gold Properties Team
 * @version 1.0
 * @since 2025-08-21
 */
class ErrorMessageService
{
    /**
     * Default fallback error message
     */
    private const DEFAULT_ERROR_MESSAGE = 'Something went wrong. Please try again or contact support if the problem persists.';

    /**
     * Contact information for error messages
     */
    private const CONTACT_INFO = '+91 XXXXX XXXXX';

    /**
     * Get user-friendly error message based on exception and context
     * 
     * @param \Exception $exception The exception that occurred
     * @param string $context The context where the error occurred (e.g., 'contact_form', 'property_creation')
     * @return string User-friendly error message
     */
    public function getUserFriendlyMessage(\Exception $exception, string $context = 'general'): string
    {
        $message = strtolower($exception->getMessage());
        
        // Database related errors
        if ($this->isDatabaseError($message)) {
            return $this->getDatabaseErrorMessage($context);
        }
        
        // File upload errors
        if ($this->isFileUploadError($message)) {
            return $this->getFileUploadErrorMessage($context);
        }
        
        // Validation errors
        if ($this->isValidationError($message)) {
            return $this->getValidationErrorMessage($context);
        }
        
        // Rate limiting errors
        if ($this->isRateLimitError($message)) {
            return $this->getRateLimitErrorMessage($context);
        }
        
        // Network/connection errors
        if ($this->isNetworkError($message)) {
            return $this->getNetworkErrorMessage($context);
        }
        
        // Permission/authorization errors
        if ($this->isPermissionError($message)) {
            return $this->getPermissionErrorMessage($context);
        }
        
        // Return context-specific default message
        return $this->getContextSpecificMessage($context);
    }

    /**
     * Check if error is database related
     */
    private function isDatabaseError(string $message): bool
    {
        $keywords = ['database', 'connection', 'mysql', 'sql', 'query', 'table', 'column'];
        return $this->containsKeywords($message, $keywords);
    }

    /**
     * Check if error is file upload related
     */
    private function isFileUploadError(string $message): bool
    {
        $keywords = ['upload', 'file', 'image', 'size', 'format', 'extension', 'mime'];
        return $this->containsKeywords($message, $keywords);
    }

    /**
     * Check if error is validation related
     */
    private function isValidationError(string $message): bool
    {
        $keywords = ['validation', 'required', 'invalid', 'format', 'email', 'phone'];
        return $this->containsKeywords($message, $keywords);
    }

    /**
     * Check if error is rate limiting related
     */
    private function isRateLimitError(string $message): bool
    {
        $keywords = ['rate limit', 'too many', 'throttle', 'limit exceeded'];
        return $this->containsKeywords($message, $keywords);
    }

    /**
     * Check if error is network related
     */
    private function isNetworkError(string $message): bool
    {
        $keywords = ['network', 'timeout', 'connection refused', 'unreachable', 'dns'];
        return $this->containsKeywords($message, $keywords);
    }

    /**
     * Check if error is permission related
     */
    private function isPermissionError(string $message): bool
    {
        $keywords = ['permission', 'unauthorized', 'forbidden', 'access denied', 'not allowed'];
        return $this->containsKeywords($message, $keywords);
    }

    /**
     * Check if message contains any of the keywords
     */
    private function containsKeywords(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (strpos($message, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get database error message
     */
    private function getDatabaseErrorMessage(string $context): string
    {
        switch ($context) {
            case 'contact_form':
                return 'We are experiencing technical difficulties processing your inquiry. Please try again in a few minutes or contact us directly at ' . self::CONTACT_INFO . '.';
            case 'property_creation':
            case 'property_update':
                return 'We are experiencing technical difficulties saving your property. Please try again in a few minutes.';
            case 'agent_management':
                return 'We are experiencing technical difficulties managing agent information. Please try again in a few minutes.';
            case 'login':
                return 'We are experiencing technical difficulties with login. Please try again in a few minutes.';
            default:
                return 'We are experiencing technical difficulties. Please try again in a few minutes or contact support at ' . self::CONTACT_INFO . '.';
        }
    }

    /**
     * Get file upload error message
     */
    private function getFileUploadErrorMessage(string $context): string
    {
        switch ($context) {
            case 'property_creation':
            case 'property_update':
                return 'There was an issue uploading your property images. Please try again with smaller image files (max 5MB each) in JPG, PNG, or WebP format.';
            case 'agent_management':
                return 'There was an issue uploading the profile image. Please try again with a smaller image file (max 2MB) in JPG or PNG format.';
            case 'blog_post':
                return 'There was an issue uploading the featured image. Please try again with a smaller image file (max 2MB) in JPG or PNG format.';
            default:
                return 'There was an issue uploading your file. Please try again with a smaller file or contact support if the problem persists.';
        }
    }

    /**
     * Get validation error message
     */
    private function getValidationErrorMessage(string $context): string
    {
        switch ($context) {
            case 'contact_form':
                return 'Please check your contact information and try again. Make sure all required fields are filled correctly.';
            case 'property_creation':
            case 'property_update':
                return 'Please check your property information and try again. Make sure all required fields are filled correctly.';
            case 'agent_management':
                return 'Please check the agent information and try again. Make sure all required fields are filled correctly.';
            case 'login':
                return 'Please check your email and password and try again.';
            default:
                return 'Please check your information and try again. Make sure all required fields are filled correctly.';
        }
    }

    /**
     * Get rate limit error message
     */
    private function getRateLimitErrorMessage(string $context): string
    {
        switch ($context) {
            case 'contact_form':
                return 'You have submitted too many inquiries recently. Please wait a while before submitting again or contact us directly at ' . self::CONTACT_INFO . '.';
            case 'login':
                return 'Too many login attempts. Please wait a few minutes before trying again.';
            default:
                return 'You have made too many requests recently. Please wait a while before trying again.';
        }
    }

    /**
     * Get network error message
     */
    private function getNetworkErrorMessage(string $context): string
    {
        return 'We are experiencing connectivity issues. Please check your internet connection and try again.';
    }

    /**
     * Get permission error message
     */
    private function getPermissionErrorMessage(string $context): string
    {
        switch ($context) {
            case 'agent_management':
                return 'You do not have permission to perform this action. Please contact your administrator.';
            case 'property_management':
                return 'You do not have permission to manage properties. Please contact your administrator.';
            default:
                return 'You do not have permission to perform this action. Please contact support if you believe this is an error.';
        }
    }

    /**
     * Get context-specific default message
     */
    private function getContextSpecificMessage(string $context): string
    {
        switch ($context) {
            case 'contact_form':
                return 'Sorry, there was an error submitting your inquiry. Please try again or contact us directly at ' . self::CONTACT_INFO . '.';
            case 'property_creation':
                return 'Failed to create property. Please check your information and try again.';
            case 'property_update':
                return 'Failed to update property. Please check your information and try again.';
            case 'agent_management':
                return 'Failed to manage agent information. Please check your information and try again.';
            case 'login':
                return 'Login failed. Please check your credentials and try again.';
            case 'blog_post':
                return 'Failed to manage blog post. Please check your information and try again.';
            default:
                return self::DEFAULT_ERROR_MESSAGE;
        }
    }

    /**
     * Log technical error details for debugging
     * 
     * @param \Exception $exception The exception that occurred
     * @param string $context The context where the error occurred
     * @param array $additionalData Additional data to log
     */
    public function logTechnicalError(\Exception $exception, string $context = 'general', array $additionalData = []): void
    {
        $logData = [
            'context' => $context,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ];

        if (!empty($additionalData)) {
            $logData['additional_data'] = $additionalData;
        }

        log_message('error', "[$context] " . $exception->getMessage() . ' | File: ' . $exception->getFile() . ' | Line: ' . $exception->getLine());
        
        // Log additional data if provided
        if (!empty($additionalData)) {
            log_message('info', "[$context] Additional data: " . json_encode($additionalData));
        }
    }
}

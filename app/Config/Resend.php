<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Resend extends BaseConfig
{
    /**
     * Resend API Key
     * Get this from your Resend dashboard: https://resend.com/api-keys
     */
    public string $apiKey = '';

    /**
     * Default sender email address
     * Must be a verified domain in your Resend account
     */
    public string $fromEmail = 'connect@goldproperties.in';

    /**
     * Default sender name
     */
    public string $fromName = 'Gold Properties';

    /**
     * Admin email address for notifications
     */
    public string $adminEmail = 'connect@goldproperties.in';

    /**
     * Resend API endpoint (used only for cURL fallback)
     */
    public string $apiEndpoint = 'https://api.resend.com/emails';

    /**
     * Email templates
     */
    public array $templates = [
        'agent_welcome' => [
            'subject' => 'Welcome to Our Gold Properties Team!',
            'template' => 'emails/agent_welcome'
        ],
        'contact_notification' => [
            'subject' => 'New Contact Form Submission',
            'template' => 'emails/contact_notification'
        ],
        'password_reset' => [
            'subject' => 'Password Reset Instructions',
            'template' => 'emails/password_reset'
        ],
    ];

    public function __construct()
    {
        // Read configuration from environment variables when available
        $this->apiKey     = (string) (env('RESEND_API_KEY') ?? $this->apiKey);
        $this->fromEmail  = (string) (env('RESEND_FROM_EMAIL') ?? $this->fromEmail);
        $this->fromName   = (string) (env('RESEND_FROM_NAME') ?? $this->fromName);
        $this->adminEmail = (string) (env('ADMIN_EMAIL') ?? $this->adminEmail);
    }
}

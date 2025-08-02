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
    public string $fromEmail = 'noreply@yourdomain.com';

    /**
     * Default sender name
     */
    public string $fromName = 'Real Estate Admin';

    /**
     * Admin email address for notifications
     */
    public string $adminEmail = 'admin@yourdomain.com';

    /**
     * Resend API endpoint
     */
    public string $apiEndpoint = 'https://api.resend.com/emails';

    /**
     * Email templates
     */
    public array $templates = [
        'agent_welcome' => [
            'subject' => 'Welcome to Our Real Estate Team!',
            'template' => 'emails/agent_welcome'
        ],
        'contact_notification' => [
            'subject' => 'New Contact Form Submission',
            'template' => 'emails/contact_notification'
        ]
    ];
}

<?php

namespace App\Services;

use Config\Resend;

class EmailService
{
    protected $config;

    public function __construct()
    {
        $this->config = new Resend();
    }

    /**
     * Send welcome email to new agent
     */
    public function sendAgentWelcomeEmail(array $agentData): bool
    {
        try {
            $template = $this->config->templates['agent_welcome'] ?? ['subject' => 'Welcome to Our White Rock Realtor Team!'];

            $emailData = [
                'from' => $this->config->fromName . ' <' . $this->config->fromEmail . '>',
                'to' => [$agentData['email']],
                'subject' => $template['subject'],
                'html' => $this->renderTemplate('agent_welcome', $agentData)
            ];

            return $this->sendEmail($emailData);
        } catch (\Exception $e) {
            log_message('error', 'Failed to send agent welcome email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send contact form notification to admin
     */
    public function sendContactNotification(array $contactData): bool
    {
        try {
            $template = $this->config->templates['contact_notification'] ?? ['subject' => 'New Contact Form Submission'];

            $emailData = [
                'from' => $this->config->fromName . ' <' . $this->config->fromEmail . '>',
                'to' => [$this->config->adminEmail],
                'subject' => $template['subject'],
                'html' => $this->renderTemplate('contact_notification', $contactData)
            ];

            return $this->sendEmail($emailData);
        } catch (\Exception $e) {
            log_message('error', 'Failed to send contact notification email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $email, string $resetLink, ?string $name = null): bool
    {
        try {
            $subject = $this->config->templates['password_reset']['subject'] ?? 'Password Reset Instructions';
            $html = $this->renderPasswordResetTemplate($resetLink, $name);

            $emailData = [
                'from' => $this->config->fromName . ' <' . $this->config->fromEmail . '>',
                'to' => [$email],
                'subject' => $subject,
                'html' => $html,
            ];

            return $this->sendEmail($emailData);
        } catch (\Exception $e) {
            log_message('error', 'Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email using Resend SDK if available, otherwise fallback to cURL
     */
    private function sendEmail(array $emailData): bool
    {
        $apiKey = $this->config->apiKey;

        // If API key is not configured, just log and return true for development
        if (empty($apiKey)) {
            log_message('info', 'Email would be sent (no API key configured): ' . json_encode($emailData));
            return true;
        }

        // Try using official Resend PHP SDK if available
        try {
            if (class_exists('Resend')) {
                $client = \Resend::client($apiKey);
                $client->emails->send($emailData);
                log_message('info', 'Email sent successfully via Resend SDK');
                return true;
            }
        } catch (\Throwable $t) {
            log_message('warning', 'Resend SDK send failed, falling back to cURL: ' . $t->getMessage());
            // fall through to cURL
        }

        // Fallback: direct HTTP call to Resend API
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->config->apiEndpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($emailData),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            log_message('error', 'Resend API cURL error: ' . $error);
            return false;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            log_message('info', 'Email sent successfully via Resend API');
            return true;
        }

        log_message('error', 'Resend API error: HTTP ' . $httpCode . ' - ' . $response);
        return false;
    }

    /**
     * Render email template
     */
    private function renderTemplate(string $template, array $data): string
    {
        switch ($template) {
            case 'agent_welcome':
                return $this->renderAgentWelcomeTemplate($data);
            case 'contact_notification':
                return $this->renderContactNotificationTemplate($data);
            default:
                return 'Email template not found.';
        }
    }

    /**
     * Render agent welcome email template
     */
    private function renderAgentWelcomeTemplate(array $data): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Welcome to Our Team</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Welcome to Our White Rock Realtor Team!</h1>
                </div>
                <div class="content">
                    <h2>Hello ' . esc($data['name']) . ',</h2>
                    <p>We are excited to welcome you to our real estate team! Your account has been successfully created and you are now part of our professional network.</p>

                    <h3>Your Account Details:</h3>
                    <ul>
                        <li><strong>Name:</strong> ' . esc($data['name']) . '</li>
                        <li><strong>Email:</strong> ' . esc($data['email']) . '</li>
                        <li><strong>Phone:</strong> ' . esc($data['phone']) . '</li>
                        ' . (!empty($data['qualification']) ? '<li><strong>Qualification:</strong> ' . esc($data['qualification']) . '</li>' : '') . '
                    </ul>

                    <p>As a member of our team, you will have access to:</p>
                    <ul>
                        <li>Property listings and management tools</li>
                        <li>Client management system</li>
                        <li>Marketing resources and support</li>
                        <li>Training and development opportunities</li>
                    </ul>

                    <p>If you have any questions or need assistance, please don\'t hesitate to contact our admin team.</p>

                    <p>Welcome aboard!</p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' White Rock Realtor Company. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Render contact notification email template
     */
    private function renderContactNotificationTemplate(array $data): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>New Contact Form Submission</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 14px; }
                .info-box { background: white; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>New Contact Form Submission</h1>
                </div>
                <div class="content">
                    <p>You have received a new contact form submission from your website.</p>

                    <div class="info-box">
                        <h3>Contact Details:</h3>
                        <p><strong>Name:</strong> ' . esc($data['name']) . '</p>
                        <p><strong>Email:</strong> ' . esc($data['email']) . '</p>
                        <p><strong>Phone:</strong> ' . esc($data['phone']) . '</p>
                        <p><strong>Property Interest:</strong> ' . esc($data['properties_in']) . '</p>
                        <p><strong>Submitted:</strong> ' . date('M j, Y g:i A') . '</p>
                    </div>

                    <div class="info-box">
                        <h3>Message:</h3>
                        <p>' . nl2br(esc($data['message'])) . '</p>
                    </div>

                    <p>Please respond to this inquiry as soon as possible to provide excellent customer service.</p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' White Rock Realtor Company. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Render password reset template
     */
    private function renderPasswordResetTemplate(string $resetLink, ?string $name = null): string
    {
        $greeting = $name ? 'Hello ' . esc($name) . ',' : 'Hello,';
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Password Reset</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0d6efd; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 24px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px; }
                .muted { color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Password Reset Request</h1>
                </div>
                <div class="content">
                    <p>' . $greeting . '</p>
                    <p>We received a request to reset your password. Click the button below to set a new password. This link will expire in 1 hour.</p>
                    <p style="text-align:center; margin: 24px 0;">
                        <a class="btn" href="' . esc($resetLink) . '" target="_blank" rel="noopener">Reset Password</a>
                    </p>
                    <p class="muted">If you did not request a password reset, you can safely ignore this email.</p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' White Rock Realtor Company. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
    }
}

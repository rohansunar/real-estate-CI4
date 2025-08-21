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
     * Send contact form confirmation email to user
     */
    public function sendContactConfirmation(array $contactData): bool
    {
        try {
            $template = $this->config->templates['contact_confirmation'] ?? ['subject' => 'Thank you for contacting White Rock Realtor'];

            $emailData = [
                'from' => $this->config->fromName . ' <' . $this->config->fromEmail . '>',
                'to' => [$contactData['email']],
                'subject' => $template['subject'],
                'html' => $this->renderTemplate('contact_confirmation', $contactData)
            ];

            return $this->sendEmail($emailData);
        } catch (\Exception $e) {
            log_message('error', 'Failed to send contact confirmation email: ' . $e->getMessage());
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
            case 'contact_confirmation':
                return $this->renderContactConfirmationTemplate($data);
            default:
                return 'Email template not found.';
        }
    }

    /**
     * Render agent welcome email template
     */
    private function renderAgentWelcomeTemplate(array $data): string
    {
        $uniqueAgentId = isset($data['unique_agent_id']) ? esc($data['unique_agent_id']) : 'Auto-generated';
        $plainPassword = isset($data['plain_password']) ? esc($data['plain_password']) : 'Not available';

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Welcome to Our Team</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    margin: 0;
                    padding: 0;
                    background-color: #f8f9fa;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background: linear-gradient(135deg, #007bff, #0056b3);
                    color: white;
                    padding: 30px 20px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 600;
                }
                .content {
                    padding: 30px 20px;
                }
                .content h2 {
                    color: #007bff;
                    margin-top: 0;
                    font-size: 20px;
                }
                .credentials-box {
                    background: #e3f2fd;
                    padding: 20px;
                    border-radius: 8px;
                    margin: 20px 0;
                    border-left: 4px solid #007bff;
                }
                .credentials-box h3 {
                    margin-top: 0;
                    color: #0056b3;
                    font-size: 18px;
                }
                .credential-item {
                    background: white;
                    padding: 12px;
                    margin: 8px 0;
                    border-radius: 4px;
                    border: 1px solid #dee2e6;
                }
                .credential-label {
                    font-weight: 600;
                    color: #495057;
                }
                .credential-value {
                    font-family: "Courier New", monospace;
                    background: #f8f9fa;
                    padding: 4px 8px;
                    border-radius: 4px;
                    color: #007bff;
                    font-weight: 600;
                }
                .security-notice {
                    background: #fff3cd;
                    border: 1px solid #ffeaa7;
                    padding: 15px;
                    border-radius: 8px;
                    margin: 20px 0;
                }
                .btn {
                    display: inline-block;
                    padding: 12px 24px;
                    background: #007bff;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: 600;
                }
                .footer {
                    padding: 20px;
                    text-align: center;
                    color: #6c757d;
                    font-size: 14px;
                    background: #f8f9fa;
                    border-top: 1px solid #dee2e6;
                }
                .highlight {
                    color: #007bff;
                    font-weight: 600;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎉 Welcome to White Rock Realtor Team!</h1>
                </div>
                <div class="content">
                    <h2>Hello ' . esc($data['name']) . ',</h2>
                    <p>We are excited to welcome you to our real estate team! Your account has been successfully created and you are now part of our professional network.</p>

                    <div class="credentials-box">
                        <h3>🔐 Your Login Credentials</h3>
                        <p>Please use the following credentials to access your agent dashboard:</p>

                        <div class="credential-item">
                            <div class="credential-label">Agent ID:</div>
                            <div class="credential-value">' . $uniqueAgentId . '</div>
                        </div>

                        <div class="credential-item">
                            <div class="credential-label">Email Address:</div>
                            <div class="credential-value">' . esc($data['email']) . '</div>
                        </div>

                        <div class="credential-item">
                            <div class="credential-label">Temporary Password:</div>
                            <div class="credential-value">' . $plainPassword . '</div>
                        </div>

                        <p style="text-align: center; margin-top: 20px;">
                            <a href="' . base_url('/agent/login') . '" class="btn">Login to Your Dashboard</a>
                        </p>
                    </div>

                    <div class="security-notice">
                        <h4 style="margin-top: 0; color: #856404;">🔒 Important Security Information</h4>
                        <ul style="margin-bottom: 0;">
                            <li>Please change your password after your first login</li>
                            <li>Keep your login credentials secure and confidential</li>
                            <li>Do not share your password with anyone</li>
                            <li>Contact admin immediately if you suspect unauthorized access</li>
                        </ul>
                    </div>

                    <h3>Your Account Details:</h3>
                    <ul>
                        <li><strong>Name:</strong> ' . esc($data['name']) . '</li>
                        <li><strong>Email:</strong> ' . esc($data['email']) . '</li>
                        <li><strong>Phone:</strong> ' . esc($data['phone']) . '</li>
                        ' . (!empty($data['qualification']) ? '<li><strong>Qualification:</strong> ' . esc($data['qualification']) . '</li>' : '') . '
                    </ul>

                    <h3>Next Steps:</h3>
                    <ol>
                        <li>Click the "Login to Your Dashboard" button above</li>
                        <li>Use your email and temporary password to log in</li>
                        <li>Change your password in the profile settings</li>
                        <li>Complete your profile information</li>
                        <li>Start managing your real estate business!</li>
                    </ol>

                    <p>If you have any questions or need assistance, please don\'t hesitate to contact our admin team at <span class="highlight">admin@whiterockrealtor.com</span> or call us at <span class="highlight">+91 XXXXX XXXXX</span>.</p>

                    <p>Welcome aboard and we look forward to your success!</p>

                    <p>Best regards,<br>
                    <strong>The White Rock Realtor Team</strong></p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' White Rock Realtor Company. All rights reserved.</p>
                    <p style="color: #6c757d; font-size: 12px; margin-top: 10px;">This email contains sensitive login information. Please keep it secure.</p>
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

    /**
     * Render contact confirmation email template for users
     */
    private function renderContactConfirmationTemplate(array $data): string
    {
        $name = esc($data['name']);
        $location = esc($data['properties_in']);
        $message = esc($data['message']);

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Thank You for Your Inquiry</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    margin: 0;
                    padding: 0;
                    background-color: #f8f9fa;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background: linear-gradient(135deg, #0d6efd, #0056b3);
                    color: white;
                    padding: 30px 20px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 600;
                }
                .content {
                    padding: 30px 20px;
                }
                .content h2 {
                    color: #0d6efd;
                    margin-top: 0;
                    font-size: 20px;
                }
                .inquiry-details {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    margin: 20px 0;
                    border-left: 4px solid #0d6efd;
                }
                .inquiry-details h3 {
                    margin-top: 0;
                    color: #495057;
                    font-size: 16px;
                }
                .contact-info {
                    background: #e3f2fd;
                    padding: 20px;
                    border-radius: 8px;
                    margin: 20px 0;
                }
                .footer {
                    padding: 20px;
                    text-align: center;
                    color: #6c757d;
                    font-size: 14px;
                    background: #f8f9fa;
                    border-top: 1px solid #dee2e6;
                }
                .highlight {
                    color: #0d6efd;
                    font-weight: 600;
                }
                .muted {
                    color: #6c757d;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🏠 Thank You for Your Inquiry!</h1>
                </div>
                <div class="content">
                    <h2>Dear ' . $name . ',</h2>
                    <p>Thank you for reaching out to <strong>White Rock Realtor</strong>! We have received your inquiry about properties in <span class="highlight">' . $location . '</span> and appreciate your interest in our services.</p>

                    <div class="inquiry-details">
                        <h3>📋 Your Inquiry Summary:</h3>
                        <p><strong>Location of Interest:</strong> ' . $location . '</p>
                        <p><strong>Your Message:</strong></p>
                        <p style="font-style: italic; margin-left: 15px;">"' . $message . '"</p>
                    </div>

                    <h2>What Happens Next?</h2>
                    <p>Our experienced real estate team will review your requirements and get back to you within <span class="highlight">24 hours</span> with:</p>
                    <ul>
                        <li>🏡 Curated property listings matching your criteria</li>
                        <li>📊 Current market insights for ' . $location . '</li>
                        <li>📅 Scheduling options for property viewings</li>
                        <li>💡 Expert advice tailored to your needs</li>
                    </ul>

                    <div class="contact-info">
                        <h3>📞 Need Immediate Assistance?</h3>
                        <p>Feel free to contact us directly:</p>
                        <p><strong>Phone:</strong> +91 XXXXX XXXXX<br>
                        <strong>Email:</strong> info@whiterockrealtor.com<br>
                        <strong>Office Hours:</strong> Monday - Saturday, 9:00 AM - 7:00 PM</p>
                    </div>

                    <p>We look forward to helping you find your perfect property!</p>
                    <p>Best regards,<br>
                    <strong>The White Rock Realtor Team</strong></p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' White Rock Realtor Company. All rights reserved.</p>
                    <p class="muted">This is an automated confirmation email. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>';
    }
}

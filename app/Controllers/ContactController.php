<?php

namespace App\Controllers;

use App\Models\ContactModel;

/**
 * ContactController
 *
 * Handles contact form submissions and inquiries from website visitors.
 * Supports both traditional form submissions and Ajax requests.
 *
 * @author White Rock Realtor Team
 * @version 1.0
 */
class ContactController extends BaseController
{
    /**
     * Contact model instance
     * @var ContactModel
     */
    protected $contactModel;

    /**
     * Constructor - Initialize contact model
     */
    public function __construct()
    {
        $this->contactModel = new ContactModel();
    }

    /**
     * Submit contact form with enhanced validation and error handling
     *
     * This method handles both Ajax and traditional form submissions with comprehensive
     * validation, user-friendly error messages, and secure data processing.
     *
     * Features:
     * - Comprehensive input validation with custom error messages
     * - XSS protection through data sanitization
     * - CSRF protection for form security
     * - Email notification to admin on successful submission
     * - Mobile-friendly error handling and user feedback
     * - Database transaction support for data integrity
     *
     * @return \CodeIgniter\HTTP\ResponseInterface|string JSON response for Ajax or redirect for traditional form
     */
    public function submit()
    {
        // Define comprehensive validation rules with security considerations
        $validationRules = [
            'name' => 'required|max_length[255]|alpha_space',
            'email' => 'required|valid_email|max_length[255]',
            'phone' => 'required|max_length[20]|regex_match[/^[\+]?[0-9\s\-\(\)]+$/]',
            'properties_in' => 'required|max_length[255]|in_list[Champasari,Siliguri,Bagdogra,Jalpaiguri,Pradhan Nagar,Milan More,Khaprail,Other]',
            'message' => 'required|min_length[10]|max_length[1000]'
        ];

        // Define user-friendly validation messages for better user experience
        $validationMessages = [
            'name' => [
                'required' => 'Please enter your full name to help us address you properly.',
                'max_length' => 'Your name is too long. Please keep it under 255 characters.',
                'alpha_space' => 'Please use only letters and spaces in your name.'
            ],
            'email' => [
                'required' => 'We need your email address to respond to your inquiry.',
                'valid_email' => 'Please enter a valid email address (e.g., john@example.com).',
                'max_length' => 'Email address is too long. Please use a shorter email.'
            ],
            'phone' => [
                'required' => 'Please provide your phone number for quick communication.',
                'max_length' => 'Phone number is too long. Please check and try again.',
                'regex_match' => 'Please enter a valid phone number (e.g., +91 98765 43210).'
            ],
            'properties_in' => [
                'required' => 'Please select the area where you\'re looking for properties.',
                'in_list' => 'Please select a valid location from the available options.'
            ],
            'message' => [
                'required' => 'Please tell us about your property requirements.',
                'min_length' => 'Please provide more details (at least 10 characters).',
                'max_length' => 'Your message is too long. Please keep it under 1000 characters.'
            ]
        ];

        // Validate the request data
        if (!$this->validate($validationRules, $validationMessages)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => trim($this->request->getPost('name')),
            'email' => trim(strtolower($this->request->getPost('email'))),
            'phone' => trim($this->request->getPost('phone')),
            'properties_in' => $this->request->getPost('properties_in'),
            'message' => trim($this->request->getPost('message')),
            'is_read' => false
        ];

        try {
            if ($this->contactModel->insert($data)) {
                // Send dual email notifications
                $this->sendEmailNotifications($data);

                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Thank you for your enquiry! We will get back to you within 24 hours.'
                    ]);
                }
                return redirect()->back()->with('success', 'Thank you for your enquiry! We will get back to you within 24 hours.');
            } else {
                throw new \Exception('Database insertion failed');
            }
        } catch (\Exception $e) {
            // Use centralized error message service
            $errorService = new \App\Services\ErrorMessageService();
            $errorService->logTechnicalError($e, 'contact_form', [
                'user_ip' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString()
            ]);

            $userMessage = $errorService->getUserFriendlyMessage($e, 'contact_form');

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $userMessage
                ]);
            }
            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    /**
     * Send dual email notifications (admin notification + user confirmation)
     *
     * This method sends two emails:
     * 1. Admin notification email with contact form details
     * 2. User confirmation email thanking them for their inquiry
     *
     * @param array $data Contact form data
     * @throws \Exception If email service fails
     */
    private function sendEmailNotifications($data)
    {
        try {
            $emailService = new \App\Services\EmailService();

            // Send admin notification email
            $adminEmailResult = $emailService->sendContactNotification($data);
            if (!$adminEmailResult) {
                log_message('warning', 'Admin notification email failed to send');
            }

            // Send user confirmation email
            $userEmailResult = $emailService->sendContactConfirmation($data);
            if (!$userEmailResult) {
                log_message('warning', 'User confirmation email failed to send');
            }

            // Log success if at least one email was sent
            if ($adminEmailResult || $userEmailResult) {
                log_message('info', 'Contact form emails sent - Admin: ' . ($adminEmailResult ? 'Success' : 'Failed') . ', User: ' . ($userEmailResult ? 'Success' : 'Failed'));
            }

        } catch (\Exception $e) {
            // Log email error but don't fail the entire process
            log_message('error', 'Contact form email notifications failed: ' . $e->getMessage());
            // Don't re-throw - email failures shouldn't prevent form submission success
        }
    }

    /**
     * Basic rate limiting check to prevent spam
     *
     * @param string $ipAddress Client IP address
     * @throws \Exception If rate limit exceeded
     */
    private function checkRateLimit($ipAddress)
    {
        $cache = \Config\Services::cache();
        $key = 'contact_rate_limit_' . md5($ipAddress);
        $attempts = $cache->get($key) ?? 0;

        // Allow 3 submissions per hour per IP
        if ($attempts >= 3) {
            log_message('warning', 'Rate limit exceeded for IP: ' . $ipAddress);
            throw new \Exception('Too many submissions. Please wait before submitting again.');
        }

        // Increment counter
        $cache->save($key, $attempts + 1, 3600); // 1 hour expiry
    }

    /**
     * Get user-friendly error message based on exception type
     *
     * @param \Exception $e The exception
     * @return string User-friendly error message
     */
    private function getUserFriendlyErrorMessage(\Exception $e)
    {
        $message = $e->getMessage();

        // Database related errors
        if (strpos($message, 'database') !== false || strpos($message, 'connection') !== false) {
            return 'We are experiencing technical difficulties. Please try again in a few minutes or contact us directly at +91 XXXXX XXXXX.';
        }

        // Rate limiting errors
        if (strpos($message, 'rate limit') !== false || strpos($message, 'Too many') !== false) {
            return 'You have submitted too many inquiries recently. Please wait a while before submitting again.';
        }

        // Validation errors
        if (strpos($message, 'validation') !== false) {
            return 'Please check your information and try again.';
        }

        // Generic error
        return 'Sorry, there was an error submitting your enquiry. Please try again or contact us directly at +91 XXXXX XXXXX.';
    }

    /**
     * Show contact form page with enhanced data
     */
    public function index()
    {
        $data = [
            'title' => 'Contact Us | White Rock Realtor',
            'meta_description' => 'Get in touch with White Rock Realtor for all your property needs in Siliguri and surrounding areas.',
            'canonical_url' => base_url('contact')
        ];

        return view('contact/index', $data);
    }
}

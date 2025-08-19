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
     * Submit contact form
     *
     * Handles both Ajax and traditional form submissions.
     * Validates input data and saves to database.
     * Sends notification email to admin.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface|string
     */
    public function submit()
    {
        // Define validation rules and custom messages
        $validationRules = [
            'name' => 'required|max_length[255]|alpha_space',
            'email' => 'required|valid_email',
            'phone' => 'required|max_length[20]|regex_match[/^[\+]?[0-9\s\-\(\)]+$/]',
            'properties_in' => 'required|max_length[255]',
            'message' => 'required|min_length[10]|max_length[1000]'
        ];

        $validationMessages = [
            'name' => [
                'required' => 'Please enter your full name',
                'alpha_space' => 'Name can only contain letters and spaces'
            ],
            'email' => [
                'required' => 'Please enter your email address',
                'valid_email' => 'Please enter a valid email address'
            ],
            'phone' => [
                'required' => 'Please enter your phone number',
                'regex_match' => 'Please enter a valid phone number'
            ],
            'properties_in' => [
                'required' => 'Please select your property interest'
            ],
            'message' => [
                'required' => 'Please enter your message',
                'min_length' => 'Message must be at least 10 characters long',
                'max_length' => 'Message cannot exceed 1000 characters'
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
                // Send notification email (optional)
                $this->sendNotificationEmail($data);

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
            // Log detailed error for debugging
            log_message('error', 'Contact form submission failed: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());

            // User-friendly error messages based on error type
            $userMessage = 'Sorry, there was an error submitting your enquiry. Please try again or contact us directly.';

            if (strpos($e->getMessage(), 'database') !== false || strpos($e->getMessage(), 'connection') !== false) {
                $userMessage = 'We are experiencing technical difficulties. Please try again in a few minutes or contact us directly.';
            } elseif (strpos($e->getMessage(), 'validation') !== false) {
                $userMessage = 'Please check your information and try again.';
            }

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
     * Send notification email to admin
     */
    private function sendNotificationEmail($data)
    {
        $emailService = new \App\Services\EmailService();
        $emailService->sendContactNotification($data);
    }

    /**
     * Show contact form page
     */
    public function index()
    {
        $data = [
            'title' => 'Contact Us | White Rock Realtor'
        ];

        return view('contact/index', $data);
    }
}

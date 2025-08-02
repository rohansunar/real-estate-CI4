<?php

namespace App\Controllers;

use App\Models\NewsletterModel;

class NewsletterController extends BaseController
{
    protected $newsletterModel;

    public function __construct()
    {
        $this->newsletterModel = new NewsletterModel();
    }

    /**
     * Subscribe to newsletter
     */
    public function subscribe()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'email' => 'required|valid_email|is_unique[newsletter.email]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $validation->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');

        // Check if already subscribed
        if ($this->newsletterModel->isSubscribed($email)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email is already subscribed to our newsletter.'
                ]);
            }
            return redirect()->back()->with('error', 'Email is already subscribed to our newsletter.');
        }

        $data = ['email' => $email];

        if ($this->newsletterModel->insert($data)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Successfully subscribed to our newsletter!'
                ]);
            }
            return redirect()->back()->with('success', 'Successfully subscribed to our newsletter!');
        } else {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to subscribe. Please try again.'
                ]);
            }
            return redirect()->back()->with('error', 'Failed to subscribe. Please try again.');
        }
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe()
    {
        $email = $this->request->getPost('email');

        if (!$email) {
            return redirect()->back()->with('error', 'Email is required.');
        }

        $subscriber = $this->newsletterModel->where('email', $email)->first();

        if (!$subscriber) {
            return redirect()->back()->with('error', 'Email not found in our newsletter list.');
        }

        if ($this->newsletterModel->delete($subscriber['id'])) {
            return redirect()->back()->with('success', 'Successfully unsubscribed from our newsletter.');
        } else {
            return redirect()->back()->with('error', 'Failed to unsubscribe. Please try again.');
        }
    }
}

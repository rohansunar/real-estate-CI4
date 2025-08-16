<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PasswordResetModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $passwordResetModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->passwordResetModel = new PasswordResetModel();
    }

    /**
     * Show login form
     */
    public function loginForm()
    {
        // Redirect if already logged in
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login | Real Estate'
        ];

        return view('auth/login', $data);
    }

    /**
     * Process login
     */
    public function login()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password');
        }

        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password');
        }

        // Set session data
        $sessionData = [
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'logged_in' => true
        ];

        session()->set($sessionData);

        return redirect()->to('/dashboard')->with('success', 'Login successful');
    }

    /**
     * Process logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Successfully logged out');
    }

    /**
     * Show forgot password form
     */
    public function forgotPasswordForm()
    {
        $data = [
            'title' => 'Forgot Password | Real Estate'
        ];

        return view('auth/forgot_password', $data);
    }

    /**
     * Process forgot password request
     */
    public function forgotPassword()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'email' => 'required|valid_email'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');

        // Check if user exists
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            // Don't reveal if email exists or not for security
            return redirect()->back()->with('success', 'If your email is registered, you will receive a password reset link shortly.');
        }

        // Generate reset token
        $token = $this->passwordResetModel->createResetToken($email);
        if (!$token) {
            return redirect()->back()->with('error', 'Unable to generate reset token. Please try again.');
        }

        // Build reset link and send via EmailService (Resend)
        $resetLink = base_url("auth/reset-password?token={$token}&email=" . urlencode($email));
        // log_message('info', 'Reset link: ' . $resetLink);
        $emailService = new \App\Services\EmailService();
        $emailService->sendPasswordResetEmail($email, $resetLink, $user['name'] ?? null);

        return redirect()->back()->with('success', 'If your email is registered, you will receive password reset instructions shortly.');
    }

    /**
     * Show password reset form
     */
    public function resetPasswordForm()
    {
        $token = $this->request->getGet('token');
        $email = $this->request->getGet('email');

        if (!$token || !$email) {
            return redirect()->to('/auth/login')->with('error', 'Invalid reset link.');
        }

        // Validate token
        if (!$this->passwordResetModel->validateToken($token, $email)) {
            return redirect()->to('/auth/login')->with('error', 'Invalid or expired reset token.');
        }

        $data = [
            'title' => 'Reset Password | Real Estate',
            'token' => $token,
            'email' => $email
        ];

        return view('auth/reset_password', $data);
    }

    /**
     * Process password reset
     */
    public function resetPassword()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'token' => 'required',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $token = $this->request->getPost('token');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validate token
        if (!$this->passwordResetModel->validateToken($token, $email)) {
            return redirect()->to('/auth/login')->with('error', 'Invalid or expired reset token.');
        }

        // Update user password
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return redirect()->to('/auth/login')->with('error', 'User not found.');
        }

        $updateData = ['password' => $password];
        if (!$this->userModel->update($user['id'], $updateData)) {
            return redirect()->back()->with('error', 'Failed to update password. Please try again.');
        }

        // Mark token as used
        $this->passwordResetModel->markTokenAsUsed($token, $email);

        return redirect()->to('/auth/login')->with('success', 'Password has been reset successfully. You can now log in with your new password.');
    }
}

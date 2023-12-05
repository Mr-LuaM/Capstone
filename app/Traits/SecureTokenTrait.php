<?php
// app/Traits/SecureTokenTrait.php

namespace App\Traits;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

trait SecureTokenTrait {
    protected function validateSecureToken($id, $secureToken) {
        // Retrieve the stored secure token and salt
        $storedSecureToken = session()->get('secure_token_'.$id);
        $salt = session()->get('secure_salt_'.$id);

        // Check if both stored token and salt exist
        if($storedSecureToken && $salt) {
            // Concatenate the ID, salt, and secret key for added security
            $dataToHash = $id.$salt.config('app.secret_key');

            // Use hash_hmac with SHA-256 for a secure hash
            $expectedSecureToken = hash_hmac('sha256', $dataToHash, config('app.secret_key'));

            // Use constant-time comparison to mitigate timing attacks
            if(hash_equals($expectedSecureToken, $secureToken)) {
                // Clear the secure token and salt after successful validation
                session()->remove('secure_token_'.$id);
                session()->remove('secure_salt_'.$id);
                return true;
            }
        }

        return false;
    }

    protected function sendVerificationEmail($email, $verificationCode) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Your SMTP server address
            $mail->SMTPAuth = true;
            $mail->Username = 'alluasan599@gmail.com'; // Your SMTP username
            $mail->Password = 'oxht neem udqo pvgn'; // Your SMTP password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Sender information
            $mail->setFrom('alluasan599@gmail.com', 'Mark Lua');
            $mail->addAddress($email);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Account Verification';
            $mail->Body = "Click the following link to verify your account: ".base_url("auth/verify/$verificationCode");

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log or handle the error as needed
            return false;
        }
    }
    // Function to check if the email is unique
    public function isEmailUnique($email) {
        // Convert the email to lowercase for case-insensitive comparison
        $email = strtolower($email);

        try {
            // Check uniqueness in applicants table
            $countApplicants = $this->applicants->where('LOWER(email)', $email)->countAllResults();

            // Check uniqueness in users table
            $countUsers = $this->users->where('LOWER(email)', $email)->countAllResults();

            // Return true if the email is unique across both tables
            return $countApplicants === 0 && $countUsers === 0;
        } catch (\Exception $e) {
            // Handle the database query exception
            log_message('error', 'Exception: '.$e);
            return false; // Consider the email as not unique in case of an error
        }
    }


}

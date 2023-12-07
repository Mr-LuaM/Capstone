<?php
// app/Traits/SecureTokenTrait.php

namespace App\Traits;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

trait SecureTokenTrait {
    protected function validateSecureToken($id, $secureToken) {
        // Retrieve the stored secure token
        $storedSecureToken = session()->get('secure_token_'.$id);

        // Validate the provided secure token against the stored one
        if($secureToken === $storedSecureToken) {
            // Clear the secure token after successful validation
            session()->remove('secure_token_'.$id);
            return true;
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
        $countApplicants = $this->applicants->where('email', $email)->countAllResults();
        $countUsers = $this->users->where('email', $email)->countAllResults();
        return $countApplicants === 0 && $countUsers === 0;
    }

}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    public function generateSecureToken($id)
    {
        // Add logic to generate a secure token based on the provided ID
        $secureToken = hash('sha256', $id . 'your_secret_key');

        // Store the secure token in the session or database for later validation
        session()->set('secure_token_' . $id, $secureToken);

        return $secureToken;
    }

}

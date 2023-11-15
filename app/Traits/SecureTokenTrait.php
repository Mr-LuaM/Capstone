<?php
// app/Traits/SecureTokenTrait.php

namespace App\Traits;

trait SecureTokenTrait
{
    public function validateSecureToken($id, $secureToken)
    {
        // Retrieve the stored secure token
        $storedSecureToken = session()->get('secure_token_' . $id);

        // Validate the provided secure token against the stored one
        if ($secureToken === $storedSecureToken) {
            // Clear the secure token after successful validation
            session()->remove('secure_token_' . $id);
            return true;
        }

        return false;
    }

}

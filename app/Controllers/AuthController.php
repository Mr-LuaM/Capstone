<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\SecureTokenTrait;
use \Firebase\JWT\JWT;

class AuthController extends BaseController {
    use SecureTokenTrait;

    public function generateSecureToken($id) {
        // Add logic to generate a secure token based on the provided ID
        $secureToken = hash('sha256', $id.'your_secret_key');

        // Store the secure token in the session or database for later validation
        session()->set('secure_token_'.$id, $secureToken);

        return $secureToken;
    }

    public function checkEmail() {
        $email = $this->request->getPost('Email');

        try {
            $user = $this->users->where('Email', $email)->first();

            if($user) {
                // Check if the user is verified
                if($user['IsVerified'] == 1) {
                    return $this->respond(['success' => true], 200);
                } else {
                    return $this->respond(['success' => false, 'error' => 'Email not verified'], 200);
                }
            } else {
                return $this->respond(['success' => false, 'error' => 'Email not found'], 200);
            }
        } catch (\Exception $e) {
            // Handle the exception, log it, or return an error response
            return $this->respond(['error' => 'An error occurred'], 500);
        }
    }

    // Inside your AuthController or wherever you handle authentication
    public function verifyAccount($verificationCode) {
        // Find the user by verification code
        $user = $this->users->where('VerificationCode', $verificationCode)->first();

        if(!$user) {
            return $this->respond('Invalid verification code', 201);
        }

        // Check if the account is already verified
        if($user['IsVerified'] == 1) {
            return $this->respond('Account is already verified', 201);
        }

        // Update user's verification status
        $updateData = [
            'IsVerified' => 1,
            //'VerificationExpiry' => date('Y-m-d H:i:s') // Optional: Set a timestamp for verification expiry
        ];

        $this->users->update($user['User_ID'], $updateData);

        return $this->respond('Account verified successfully', 200);
    }


    public function login() {
        $email = $this->request->getPost('Email');
        $password = $this->request->getPost('Password');

        try {
            // Validate user credentials
            $user = $this->users->where('Email', $email)->first();

            if(!$user) {
                return $this->respond(['error' => 'User not found'], 404);
            }



            // Check status based on role
            $status = $this->getUserStatusByRole((int)$user['Role_ID'], $user['User_ID']);

            // Check if the user is active
            if(strtolower($status) !== 'active') {
                return $this->respond(['error' => 'Account is not active'], 403);
            }


            // Verify hashed password
            if(!password_verify($password, $user['Password'])) {
                return $this->respond(['error' => 'Invalid password'], 200);
            }

            // Check if the user is verified
            // if(!$user['IsVerified']) {
            //     return $this->respond(['error' => 'Account not verified'], 403);
            // }

            // Assuming validation is successful, generate JWT token
            $tokenData = [
                'user_id' => $user['User_ID'],
                'email' => $user['Email'],
                'role' => $user['Role_ID'],
                // Add more claims as needed
            ];



            // Get the JWT secret securely
            $jwtSecret = getenv('JWT_SECRET');



            // Generate JWT token
            $token = JWT::encode($tokenData, $jwtSecret, 'HS256');

            // Log successful login (consider adding more details)
            // log('Successful login for user: ' . $user['Email']);

            return $this->respond(['token' => $token], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging and auditing
            // log('Exception during login: ' . $e->getMessage());

            // Return an error response
            return $this->respond(['error' => 'An error occurred'], 500);
        }
    }

    // Function to get user status based on role
    private function getUserStatusByRole($roleId, $userID) {
        $roleId = (int)$roleId;
        switch($roleId) {
            case 1: // Assuming role 1 corresponds to main admin
                // Check the admin table for status
                $mainAdmin = $this->mainAdmin->where('User_ID', $userID)->first();

                return $mainAdmin ? $mainAdmin['Status'] : 'inactive';
            case 2: // Assuming role 2 corresponds to station admin
                // Check the admin table for status
                $mainAdmin = $this->mainAdmin->where('User_ID', $userID)->first();
                return $mainAdmin ? $mainAdmin['Status'] : 'inactive';
            case 3: // Assuming role 2 corresponds to station admin
                // Check the admin table for status
                $stationAdmin = $this->StationAdmin->where('User_ID', $userID)->first();
                return $stationAdmin ? $stationAdmin['Status'] : 'inactive';
            case 4: // Assuming role 2 corresponds to station admin
                // Check the admin table for status
                $teachers = $this->teachers->where('User_ID', $userID)->first();
                return $teachers ? $teachers['Status'] : 'inactive';
            default:
                return '1'; // Default to inactive if role is not recognized
        }
    }


    // Function to get user status based on role




    public function getUserDetails($role, $userId) {
        // Define an array to map roles to their respective models
        $roleModels = [
            '3' => \App\Models\StationAdminModel::class,
            '6' => \App\Models\ApplicantsModel::class,
            '2' => \App\Models\MainAdminModel::class,
            '4' => \App\Models\TeachersModel::class,
            // Add more entries for other roles as needed
        ];

        // Check if the provided role exists in the mapping
        if(!array_key_exists((int)$role, $roleModels)) {
            return $this->failNotFound('Invalid role');
        }

        // Create an instance of the corresponding model based on the role
        $model = new $roleModels[$role];

        // Fetch user details based on the user ID
        $userDetails = $model->where('User_ID', (int)$userId)->first();

        // You might use the result for further processing
        if($userDetails) {
            // User details found, you can access them using $userDetails
            $fullName = $this->getFullName($userDetails);
            $additionalDetails = $this->getAdditionalDetails($role, $userDetails);

            // Combine user details and additional details
            $data = array_merge($userDetails, $additionalDetails);
            $data['fullName'] = $fullName;

            // Send the data in the response
            return $this->respond($data, 200);
        } else {
            // User not found
            // Handle the case when the user with the specified ID is not found
            return $this->failNotFound('User not found');
        }
    }

    // Function to get the full name from user details
    private function getFullName($userDetails) {
        $firstName = $userDetails['First_Name'];
        $middleName = $userDetails['Middle_Name'];
        $lastName = $userDetails['Last_Name'];

        // Create a full name by concatenating first, middle, and last names
        return trim("$firstName $middleName $lastName");
    }

    // Function to get additional details based on the role
    private function getAdditionalDetails($role, $userDetails) {
        // Add logic specific to each role to get additional details
        switch($role) {
            case 2: // Station Admin
                $stationDetails = $this->StationAdmin->where('User_ID', $userDetails['User_ID'])->first();
                if($stationDetails) {
                    return ['station' => $stationDetails['Station']];
                } else {
                    return ['station' => null]; // Return an array with a null value for station
                }
                break;
            // Add cases for other roles as needed

            default:
                return [];
        }
    }




    public function createAccount() {
        try {
            $this->db->transBegin(); // Start transaction

            // Validate form data (you may want to add more validation rules)

            // If validation passes, proceed with account creation
            $firstName = $this->request->getPost('FirstName');
            $lastName = $this->request->getPost('LastName');
            $email = $this->request->getPost('Email');
            $password = $this->request->getPost('Password');
            $role = $this->request->getPost('Role');
            $verificationCode = md5(uniqid(rand(), true));

            // Example: Insert into users table
            $this->users->save([
                'Email' => $email,
                'Password' => password_hash($password, PASSWORD_DEFAULT), // Hash the password for security
                'Role_ID' => $role,
                'VerificationCode' => $verificationCode,
            ]);

            // Get the inserted user id
            $userId = $this->users->insertID();

            $roleModels = [
                '3' => \App\Models\StationAdminModel::class,
                '6' => \App\Models\ApplicantsModel::class,
                '1' => \App\Models\MainAdminModel::class,
                // Add more entries for other roles as needed
            ];

            if(array_key_exists($role, $roleModels)) {
                $userModel = new $roleModels[$role]();

                // Insert into the corresponding table
                $userModel->save([
                    'User_ID' => $userId,
                    'First_Name' => $firstName,
                    'Last_Name' => $lastName,
                ]);

                // Commit the transaction
                $this->db->transCommit();

                $this->sendVerificationEmail($email, $verificationCode);

                return $this->response->setJSON(['success' => true, 'message' => 'Account created successfully']);
            } else {
                // Handle unknown role
                return $this->response->setJSON(['success' => false, 'message' => 'Unknown role']);
            }
        } catch (\Exception $e) {
            // Handle errors and rollback the transaction
            $this->db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Error creating account', 'error' => $e->getMessage()]);
        }
    }

}

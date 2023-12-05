<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\SecureTokenTrait;
use \Firebase\JWT\JWT;

class AuthController extends BaseController {
    use SecureTokenTrait;

    public function generateSecureToken($id) {
        // Load a secure secret key from a secure storage (e.g., environment variable)
        $secretKey = config('app.secret_key');

        // Generate a random salt
        $salt = bin2hex(random_bytes(16));

        // Concatenate the ID, salt, and secret key for added security
        $dataToHash = $id.$salt.$secretKey;

        // Use hash_hmac with SHA-256 for a secure hash
        $secureToken = hash_hmac('sha256', $dataToHash, $secretKey);

        // Store the salt in the session or database for later validation
        session()->set('secure_salt_'.$id, $salt);

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
                    return $this->respond(['success' => false, 'error' => 'Email not verified'], 403); // 403 Forbidden for unverified email
                }
            } else {
                return $this->respond(['success' => false, 'error' => 'Email not found'], 404); // 404 Not Found for non-existing email
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            log_message('error', 'Exception: '.$e);

            // Return a 500 Internal Server Error status code
            return $this->respond(['error' => 'An error occurred'], 500);
        }
    }

    // Inside your AuthController or wherever you handle authentication
    public function verifyAccount($verificationCode) {
        // Find the user by verification code
        $user = $this->users->where('VerificationCode', $verificationCode)->first();

        if(!$user) {
            // Log the attempt with an invalid verification code
            log_message('info', 'Invalid verification code attempt: '.$verificationCode);

            return $this->respond('Invalid verification code', 404); // 404 Not Found for an invalid verification code
        }

        // Check if the account is already verified
        if($user['IsVerified'] == 1) {
            // Log the attempt to verify an already verified account
            log_message('info', 'Attempt to re-verify an already verified account: '.$user['User_ID']);

            return $this->respond('Account is already verified', 400); // 400 Bad Request for an already verified account
        }

        // Update user's verification status
        $updateData = [
            'IsVerified' => 1,
            //'VerificationExpiry' => date('Y-m-d H:i:s') // Optional: Set a timestamp for verification expiry
        ];

        $this->users->update($user['User_ID'], $updateData);

        // Log the successful verification
        log_message('info', 'Account verified successfully: '.$user['User_ID']);

        return $this->respond('Account verified successfully', 200);
    }



    public function login() {
        $email = $this->request->getPost('Email');
        $password = $this->request->getPost('Password');

        try {
            // Validate user credentials
            $user = $this->users->where('Email', $email)->first();

            if(!$user) {
                // Log the failed login attempt
                log_message('info', 'Login failed for user not found: '.$email);

                return $this->respond(['error' => 'User not found'], 404);
            }

            // Check status based on role
            $status = $this->getUserStatusByRole((int)$user['Role_ID'], $user['User_ID']);

            // Check if the user is active
            if(strtolower($status) !== 'active') {
                // Log the failed login attempt
                log_message('info', 'Login failed for inactive account: '.$email);

                return $this->respond(['error' => 'Account is not active'], 403);
            }

            // Verify hashed password
            if(!password_verify($password, $user['Password'])) {
                // Log the failed login attempt
                log_message('info', 'Login failed for invalid password: '.$email);

                return $this->respond(['error' => 'Invalid password'], 401);
            }

            // Check if the user is verified
            if(!$user['IsVerified']) {
                // Log the failed login attempt
                log_message('info', 'Login failed for unverified account: '.$email);

                return $this->respond(['error' => 'Account not verified'], 403);
            }

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

            // Log successful login
            log_message('info', 'Successful login for user: '.$email);

            return $this->respond(['token' => $token], 200);
        } catch (\PDOException $e) {
            // Log the database-related exception
            log_message('error', 'Database exception during login: '.$e->getMessage());

            return $this->respond(['error' => 'Database error'], 500);
        } catch (\Exception $e) {
            // Log the general exception for debugging and auditing
            log_message('error', 'Exception during login: '.$e->getMessage());

            return $this->respond(['error' => 'An error occurred'], 500);
        }
    }


    // Function to get user status based on role
    private function getUserStatusByRole($roleId, $userID) {
        $roleId = (int)$roleId;

        switch($roleId) {
            case 1: // Main Admin
                $mainAdmin = $this->mainAdmin->where('User_ID', $userID)->first();
                return $mainAdmin ? $mainAdmin['Status'] : 'inactive';

            case 2:
                $mainAdmin = $this->mainAdmin->where('User_ID', $userID)->first();
                return $mainAdmin ? $mainAdmin['Status'] : 'inactive';

            case 3: // Station Admin
                $stationAdmin = $this->StationAdmin->where('User_ID', $userID)->first();
                return $stationAdmin ? $stationAdmin['Status'] : 'inactive';

            case 4: // Teacher
                $teacher = $this->teachers->where('User_ID', $userID)->first();
                return $teacher ? $teacher['Status'] : 'inactive';

            case 5: // Student
                $student = $this->students->where('User_ID', $userID)->first();
                return $student ? $student['Status'] : 'inactive';

            case 6: // Applicants
                $applicant = $this->applicants->where('User_ID', $userID)->first();
                return $applicant ? $applicant['Status'] : 'inactive';
            default:
                return 'inactive'; // Default to 'inactive' if role is not recognized
        }
    }



    // Function to get user status based on role




    // public function getUserDetails($role, $userId) {
    //     // Define an array to map roles to their respective models
    //     $roleModels = [
    //         '3' => \App\Models\StationAdminModel::class,
    //         '6' => \App\Models\ApplicantsModel::class,
    //         '1' => \App\Models\MainAdminModel::class,
    //         // Add more entries for other roles as needed
    //     ];

    //     // Check if the provided role exists in the mapping
    //     if (!array_key_exists($role, $roleModels)) {
    //         return $this->failNotFound('Invalid role');
    //     }

    //     // Create an instance of the corresponding model based on the role
    //     $model = new $roleModels[$role];

    //     // Fetch user details based on the user ID
    //     $userDetails = $model->where('User_ID', $userId)->first();

    //     // Check if the user is found
    //     if (!$userDetails) {
    //         // User not found
    //         // Handle the case when the user with the specified ID is not found
    //         return $this->failNotFound('User not found');
    //     }

    //     // Extract user details
    //     $fullName = trim("{$userDetails['First_Name']} {$userDetails['Middle_Name']} {$userDetails['Last_Name']}");
    //     $profilePicture = $userDetails['Profile_Picture'];

    //     // Prepare response data
    //     $data = [
    //         'fullName' => $fullName,
    //         'profilePicture' => $profilePicture,
    //         // Add other details as needed
    //     ];

    //     // Return the response
    //     return $this->respond($data, 200);
    // }


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
                '2' => \App\Models\MainAdminModel::class,
                '4' => \App\Models\TeachersModel::class,
                '5' => \App\Models\StudentsModel::class,
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

                // Send the verification email
                if($this->sendVerificationEmail($email, $verificationCode)) {
                    return $this->response->setStatusCode(201)->setJSON(['success' => true, 'message' => 'Account created successfully']);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Error sending verification email']);
                }
            } else {
                // Handle unknown role
                return $this->response->setJSON(['success' => false, 'message' => 'Unknown role']);
            }
        } catch (\Exception $e) {
            // Handle errors and rollback the transaction
            $this->db->transRollback();
            log_message('error', 'Error creating account: '.$e->getMessage());

            return $this->response->setJSON(['success' => false, 'message' => 'Error creating account', 'error' => $e->getMessage()]);
        }
    }


}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\SecureTokenTrait;

class ApplicantsController extends BaseController {
    use SecureTokenTrait;

    // public function testEmail() {
    //     // Replace these values with your actual email and verification code
    //     $email = 'marklua599@gmail.com';
    //     $verificationCode = 'your-verification-code';

    //     try {
    //         $this->sendVerificationEmail($email, $verificationCode);
    //         echo 'Email sent successfully!';
    //     } catch (\Exception $e) {
    //         echo 'Error sending email: '.$e->getMessage();
    //     }
    // }

    public function submitApplication() {
        // Start a database transaction
        $this->db->transStart();

        try {
            // Access the uploaded file
            $selectedFile1 = $this->request->getFile('selectedFile1');

            // Check if the file was uploaded successfully
            if($selectedFile1->isValid()) {
                // Generate a unique filename to avoid overwriting existing files
                $newFileName = $selectedFile1->getRandomName();

                // Move the uploaded file to a specific directory with the new filename
                $selectedFile1->move('public/uploads/applicants/profiles/', $newFileName);

                $birthdate = $this->request->getPost('Bdate');

                // Check if $birthdate is a valid date string
                if(is_string($birthdate) && strtotime($birthdate)) {
                    // Calculate age based on the birthdate
                    $birthdate = new \DateTime($birthdate);
                    $currentDate = new \DateTime();
                    $age = $currentDate->diff($birthdate)->y;
                } else {
                    // Handle the case where $birthdate is not a valid date string
                    $age = 1; // or set a default value or handle the error as needed
                }

                // Now, you can save the file path to your database along with other form fields
                $data = [
                    'Last_Name' => $this->request->getPost('lastName'),
                    'First_Name' => $this->request->getPost('firstName'),
                    'Middle_Name' => $this->request->getPost('middleName'),
                    'Name_Extension' => $this->request->getPost('nameExtension'),
                    'Sex' => $this->request->getPost('sex'),
                    'Bdate' => $this->request->getVar('Bdate'),
                    'Age' => (int)$age,
                    'Nationality' => $this->request->getPost('nationality'),
                    'Religion' => $this->request->getPost('religion'),
                    'Height' => (float)$this->request->getPost('height'),
                    'Weight' => (float)$this->request->getPost('weight'),
                    'Address' => $this->request->getPost('address'),
                    'Email' => $this->request->getPost('email'),
                    'Phone_Number' => $this->request->getPost('phoneNumber'),
                    'Selected_File1' => 'public/uploads/applicants/profiles/'.$newFileName,
                    'Course1' => $this->request->getPost('course1'),
                    'Station1' => $this->request->getPost('station1'),
                    'Course2' => $this->request->getPost('course2'),
                    'Station2' => $this->request->getPost('station2'),
                    'Course3' => $this->request->getPost('course3'),
                    'Station3' => $this->request->getPost('station3'),
                    'Date_Of_Application' => date('Y-m-d H:i:s'),
                    'Status' => 'pending', // Assuming all applications start as pending
                    'Password' => $this->request->getPost('password') // Add the password field
                ];

                // Validate input data
                $validationRules = $this->getValidationRules();
                if(!$this->validate($validationRules)) {
                    return $this->fail(validation_errors(), 400);
                }

                // Check if the email is unique
                if(!$this->isEmailUnique($data['Email'])) {
                    return $this->fail('Email is already in use', 400);
                }

                $verificationCode = md5(uniqid(rand(), true));

                // Create a user entry for the applicant
                if(!empty($data['Password'])) {
                    $userData = [
                        'Email' => $data['Email'],
                        'Password' => password_hash($data['Password'], PASSWORD_BCRYPT),
                        'Role_ID' => 6,
                        'VerificationCode' => $verificationCode,
                        'IsVerified' => 0,
                        'RegistrationDate' => date('Y-m-d H:i:s'),
                        'LastLoginDate' => null,
                        'VerificationExpiry' => null
                    ];

                    // Save the user data to the database
                    $this->users->save($userData);
                    // Get the inserted ID from the users table
                    $userID = $this->users->insertID();

                    // Add the User_ID to the applicant data
                    $data['User_ID'] = $userID;

                    // Save the data to the applicants table
                    $r = $this->applicants->save($data);

                    // Commit the transaction
                    $this->db->transComplete();

                    // Send the verification email
                    $this->sendVerificationEmail($data['Email'], $verificationCode);

                    return $this->respond($r, 200);
                }
            }

        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            $this->db->transRollback();
            // Log the exception and database errors for debugging
            log_message('error', 'Exception: '.$e);
            log_message('error', 'Database Errors: '.$this->db->error()['message']);

            return $this->fail('Error occurred: '.$e->getMessage(), 500);
        }
    }







    // Define validation rules in a separate method
    private function getValidationRules() {
        return [
            'lastName' => 'required|min_length[2]',
            'firstName' => 'required|min_length[2]',
            'sex' => 'required|in_list[Male,Female]',
            'Bdate' => 'required|valid_date',
            'nationality' => 'required|min_length[3]', // Adjust the minimum length
            'religion' => 'required|min_length[3]', // Adjust the minimum length
            'height' => 'required|numeric', // Numeric value
            'weight' => 'required|numeric', // Numeric value
            'email' => 'required|valid_email',
            'phoneNumber' => 'required|numeric|min_length[10]', // Numeric value with minimum length
        ];
    }







}


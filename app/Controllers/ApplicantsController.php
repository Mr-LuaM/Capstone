<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Request;
use App\Traits\SecureTokenTrait;

class ApplicantsController extends ResourceController
{
    use ResponseTrait;
    use SecureTokenTrait;

    private $applicants;
    private $users;
    private $students;

    public function __construct()
    {
        $this->applicants = new \App\Models\ApplicantsModel;
        $this->students = new \App\Models\StudentsModel;
        $this->users = new \App\Models\UserModel;
    }
    public function submitApplication()
    {
        // Access the uploaded file
        $selectedFile1 = $this->request->getFile('selectedFile1');

        // Check if the file was uploaded successfully
        if ($selectedFile1->isValid()) {
            // Generate a unique filename to avoid overwriting existing files
            $newFileName = $selectedFile1->getRandomName();

            // Move the uploaded file to a specific directory with the new filename
            $selectedFile1->move('public/uploads/applicants/profiles/', $newFileName);

            $birthdate = $this->request->getPost('Bdate');

            // Check if $birthdate is a valid date string
            if (is_string($birthdate) && strtotime($birthdate)) {
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
                'Age' => (int) $age,
                'Nationality' => $this->request->getPost('nationality'),
                'Religion' => $this->request->getPost('religion'),
                'Height' => (float) $this->request->getPost('height'),
                'Weight' => (float) $this->request->getPost('weight'),
                'Address' => $this->request->getPost('address'),
                'Email' => $this->request->getPost('email'),
                'Phone_Number' => $this->request->getPost('phoneNumber'),
                'Selected_File1' => 'public/uploads/applicants/profiles/' . $newFileName,
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

            // Perform validation
            if ($this->validate($this->getValidationRules(), $data)) {
                // Check if the email is unique before saving
                if ($this->isEmailUnique($data['Email'])) {
                    // Save the data to your database
                    $r = $this->applicants->save($data);

                    // Get the inserted ID
                    $applicantID = $this->applicants->insertID();

                    // Create a user entry for the applicant
                    if (!empty($data['Password'])) {
                        $userData = [
                            'Email' => $data['Email'],
                            'Password' => password_hash($data['Password'], PASSWORD_BCRYPT),
                            'Role_ID' => 6
                        ];

                        // Save the user data to the database
                        $this->users->save($userData);

                        return $this->respond($r, 200);
                    } else {
                        return $this->fail('Email is not unique', 400);
                    }
                } else {
                    // Validation failed, return an error response
                    return $this->fail($this->validator->getErrors(), 400);
                }
            } else {
                // Handle other form fields and return an appropriate response if the file upload fails
                return $this->fail('File upload failed', 400);
            }
        }
    }


    // Function to check if the email is unique
    private function isEmailUnique($email)
    {
        $countApplicants = $this->applicants->where('email', $email)->countAllResults();
        $countUsers = $this->users->where('email', $email)->countAllResults();
        return $countApplicants === 0 && $countUsers === 0;
    }

    // Define validation rules in a separate method
    private function getValidationRules()
    {
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

    public function approve()
    {
        try {
            $db = \Config\Database::connect(); // Get the database connection

            $db->transStart(); // Start a transaction

            $data = [
                'id' => $this->request->getPost('id'),
                'Last_Name' => $this->request->getPost('Last_Name'),
                'First_Name' => $this->request->getPost('First_Name'),
                'Middle_Name' => $this->request->getPost('Middle_Name'),
                'Name_Extension' => $this->request->getPost('Name_Extension'),
                'Sex' => $this->request->getPost('Sex'),
                'Bdate' => $this->request->getVar('Bdate'),
                'Age' => (int) $this->request->getVar('Age'),
                'Nationality' => $this->request->getPost('Nationality'),
                'Religion' => $this->request->getPost('Religion'),
                'Address' => $this->request->getPost('Address'),
                'Email' => $this->request->getPost('Email'),
                'Phone_Number' => $this->request->getPost('Phone_Number'),
                'Profile' => $this->request->getPost('Profile'),
                'approvedCourse' => $this->request->getPost('approvedCourse'),
                'approvedStation' => $this->request->getPost('approvedStation'),
                'Status' => $this->request->getPost('Status'),
            ];

            // Check if the applicant status is 'Pending'
            if ($data['Status'] !== 'pending') {
                // Return an error response or handle it appropriately
                return $this->fail('Applicant status must be "Pending" to approve.', 400);
            }

            // Check if the user already exists
            $existingUser = $this->users->where('Email', $data['Email'])->first();

            if ($existingUser) {
                // Check if the user is not already a student
                $existingStudent = $this->students->where('User_ID', $existingUser['User_ID'])->first();

                if (!$existingStudent) {
                    // Define user role
                    $userFields = [
                        'Role_ID' => 5,  // Set the role ID as needed
                    ];

                    // Update existing user information
                    $this->users->update($existingUser['User_ID'], $userFields);

                    $userId = $existingUser['User_ID'];

                    // Insert student data
                    $studentFields = [
                        'User_ID' => $userId,
                        'Profile' => $data['Profile'],
                        'First_Name' => $data['First_Name'],
                        'Middle_Name' => $data['Middle_Name'],
                        'Last_Name' => $data['Last_Name'],
                        'Name_Extension' => $data['Name_Extension'],
                        'Age' => $data['Age'],
                        'Sex' => $data['Sex'],
                        'Address' => $data['Address'],
                        'Birthday' => $data['Bdate'],
                        'Nationality' => $data['Nationality'],
                        'Religion' => $data['Religion'],
                        'Stud_PhoneNum' => $data['Phone_Number'],
                        // Add other fields as needed
                    ];

                    $this->students->insert($studentFields);

                    // Update applicant status to 'approved'
                    $applicantId = $data['id']; // Adjust this based on your data structure
                    $this->applicants->update($applicantId, ['Status' => 'approved']);
                } else {
                    // Return an error response or handle it appropriately
                    return $this->fail('User is already a student.', 400);
                }
            } else {
                // Return an error response or handle it appropriately
                return $this->fail('User does not exist.', 400);
            }

            $r = $db->transComplete(); // Complete the transaction
            return $this->respond($r, 200);
        } catch (\Exception $e) {
            $db->transRollback(); // Rollback the transaction on error
            log_message('error', $e->getMessage());
            // Handle the error gracefully, perhaps show a user-friendly message
            return $this->fail('An error occurred while processing the request.', 500);
        }
    }

    public function reject($id)
    {
        try {
            // Validate the input id
            if (!is_numeric($id) || $id < 1) {
                return $this->fail('Invalid applicant id.', 400);
            }

            // Fetch the secure token from the request
            $secureToken = $this->request->getPost('secureToken');


            // Validate the secure token
            if (!$this->validateSecureToken($id, $secureToken)) {
                return $this->fail('Invalid secure token.', 400);
            }

            // Find the applicant by id
            $applicant = $this->applicants->find($id);

            if (!$applicant) {
                // Applicant with the given id not found
                return $this->fail('Applicant not found.', 404);
            }

            if ($applicant['Status'] !== 'pending') {
                // Return an error response or handle it appropriately
                return $this->fail('Applicant status must be "Pending" to reject.', 400);
            }

            // Perform the rejection logic here

            // Optionally, update the applicant status
            $this->applicants->update($id, ['Status' => 'rejected']);

            return $this->respond('Applicant rejected successfully.', 200);
        } catch (\Exception $e) {
            // Handle exceptions here
            return $this->fail('An error occurred.', 500);
        }
    }


    public function test()
    {
        var_dump($this->users->where('Email', 'marklua599@gmail.com')->first());

    }





}

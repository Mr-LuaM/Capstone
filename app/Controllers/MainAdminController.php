<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\SecureTokenTrait;

class MainAdminController extends BaseController {

    use SecureTokenTrait;

    public function getApplicants() {
        $data = $this->applicants->findAll();
        if(empty($data)) {
            return $this->failNotFound('No Courses Found');
        }
        return $this->respond($data);

    }
    public function approve() {
        try {
            $this->db->transStart(); // Start a transaction

            $data = [
                'id' => $this->request->getPost('id'),
                'Last_Name' => $this->request->getPost('Last_Name'),
                'First_Name' => $this->request->getPost('First_Name'),
                'Middle_Name' => $this->request->getPost('Middle_Name'),
                'Name_Extension' => $this->request->getPost('Name_Extension'),
                'Sex' => $this->request->getPost('Sex'),
                'Bdate' => $this->request->getVar('Bdate'),
                'Age' => (int)$this->request->getVar('Age'),
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
            if($data['Status'] !== 'pending') {
                // Return an error response or handle it appropriately
                return $this->fail('Applicant status must be "Pending" to approve.', 400);
            }

            // Check if the user already exists
            $existingUser = $this->users->where('Email', $data['Email'])->first();

            if($existingUser) {
                // Check if the user is not already a student
                $existingStudent = $this->students->where('User_ID', $existingUser['User_ID'])->first();

                if(!$existingStudent) {
                    // Define user role
                    $userFields = [
                        'Role_ID' => 5,  // Set the role ID as needed
                    ];

                    // Update existing user information
                    $this->users->update($existingUser['User_ID'], $userFields);

                    $userId = $existingUser['User_ID'];

                    // $studID = $this->students->where('User_ID', $userId)->first();
                    // return $this->respond($studID, 200);
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
                    $studID = $this->students->where("User_ID", $userId)->first();
                    // Update applicant status to 'approved'
                    $applicantId = $data['id']; // Adjust this based on your data structure
                    $this->applicants->update($applicantId, ['Status' => 'approved']);


                    $course = $this->courses->where('Course_Name', $data['approvedCourse'])->first();
                    $courseID = $course['Course_ID'];

                    // Get the Station_ID based on the station name
                    $station = $this->stations->where('Station_Name', $data['approvedStation'])->first();
                    $stationID = $station['Station_ID'];
                    // Assuming you have a foreign key relationship in the database
                    // Assign the student to a specific course and station
                    $enrollmentFields = [
                        'Stud_ID' => $studID['Stud_ID'], // using the applicant ID as Stud_ID
                        'Course_ID' => intval($courseID),
                        'Station_ID' => intval($stationID),
                        // ... (other enrollment fields)
                    ];


                    $this->enrollments->insert($enrollmentFields);

                } else {
                    // Return an error response or handle it appropriately
                    return $this->fail('User is already a student.', 400);
                }
            } else {
                // Return an error response or handle it appropriately
                return $this->fail('User does not exist.', 400);
            }

            $this->db->transComplete(); // Complete the transaction
            return $this->respond(true, 200);
        } catch (\Exception $e) {
            $this->db->transRollback(); // Rollback the transaction on error
            log_message('error', $e->getMessage());
            // Handle the error gracefully, perhaps show a user-friendly message
            return $this->fail('An error occurred while processing the request.', 500);
        }
    }



    public function reject($id) {
        try {
            // Validate the input id
            if(!is_numeric($id) || $id < 1) {
                return $this->fail('Invalid applicant id.', 400);
            }

            // Fetch the secure token from the request
            $secureToken = $this->request->getPost('secureToken');


            // Validate the secure token
            if(!$this->validateSecureToken($id, $secureToken)) {
                return $this->fail('Invalid secure token.', 400);
            }

            // Find the applicant by id
            $applicant = $this->applicants->find($id);

            if(!$applicant) {
                // Applicant with the given id not found
                return $this->fail('Applicant not found.', 404);
            }

            if($applicant['Status'] !== 'pending') {
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

    public function editStation() {
        $request = service('request');

        // Validate the request data
        $this->validation->setRules([
            'Station_ID' => 'required|integer',
            'Station_Name' => 'required|string',
            'Location' => 'required|string',
            'status' => 'required|string',
            'Courses_Offered.*.Course_ID' => 'required|integer', // Assuming Course_ID is the ID
        ]);

        if(!$this->validation->withRequest($this->request)->run()) {
            return $this->failValidationErrors($this->validation->getErrors());
        }

        // Extract the data
        $stationId = $request->getPost('Station_ID');
        $stationName = $request->getPost('Station_Name');
        $location = $request->getPost('Location');
        $status = $request->getPost('status');
        $coursesOffered = $request->getPost('Courses_Offered');

        // Start a database transaction
        $this->db->transStart();

        try {
            // Save the station data
            $stationData = [
                'Station_Name' => $stationName,
                'Location' => $location,
                'status' => $status,
            ];

            $this->stations->update($stationId, $stationData);

            // Get the existing courses associated with the station
            $existingCourses = $this->stationCourses->where('Station_ID', $stationId)->findAll();
            // return $this->respond($existingCourses);
            // Extract the Course_ID values from existing courses
            $existingCourseIds = array_column($existingCourses, 'Course_ID');


            // Compare the existing courses with the courses received in the form
            foreach($coursesOffered as $courseData) {
                $courseId = $courseData['Course_ID']; // Assuming Course_ID is the ID

                if(!in_array($courseId, $existingCourseIds)) {
                    // Course is not linked to the station, so add it
                    $courseData['Station_ID'] = $stationId;
                    $this->stationCourses->insert($courseData);
                }
            }

            // Check for removed courses
            foreach($existingCourses as $existingCourse) {
                $existingCourseId = $existingCourse['Course_ID'];
                $existingStationCourseId = $existingCourse['StationCourse_ID'];

                // Check if the existing course is not present in the received form data
                if(!in_array($existingCourseId, array_column($coursesOffered, 'Course_ID'))) {
                    // Course is in the database but not in the form, so remove it
                    $this->stationCourses->delete(['StationCourse_ID' => $existingStationCourseId]);
                }
            }

            // Commit the transaction
            $this->db->transCommit();

            return $this->respond(['success' => true, 'message' => 'Station edited successfully']);
        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            $this->db->transRollback();

            return $this->respond(['success' => false, 'message' => 'Error editing station']);
        }
    }

    public function addStation() {
        $request = service('request');

        // Validate the request data
        $this->validation->setRules([
            'Station_Name' => 'required|string',
            'Location' => 'required|string',
            'status' => 'required|string',
            'Courses_Offered.*.Course_ID' => 'required|integer',
        ]);

        if(!$this->validation->withRequest($this->request)->run()) {
            return $this->failValidationErrors($this->validation->getErrors());
        }

        // Extract the data
        $stationName = $request->getPost('Station_Name');
        $location = $request->getPost('Location');
        $status = $request->getPost('status');
        $coursesOffered = $request->getPost('Courses_Offered');

        // Now you can use the extracted data for further processing, such as inserting into the database
        // You may also want to use transactions to ensure data consistency

        try {
            // Start a database transaction
            $this->db->transStart();

            // Save the station data
            $stationData = [
                'Station_Name' => $stationName,
                'Location' => $location,
                'status' => $status,
            ];


            $this->stations->insert($stationData);
            $stationId = $this->db->insertID(); // Get the ID of the inserted station
            // return $this->respond($stationId);


            // Save the associated courses
            foreach($coursesOffered as $courseData) {
                $courseId = $courseData['Course_ID'];
                $courseData['Station_ID'] = $stationId;
                $this->stationCourses->insert($courseData);
            }

            // Commit the transaction
            $this->db->transCommit();

            return $this->respond(['success' => true, 'message' => 'Station added successfully']);
        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            $this->db->transRollback();

            return $this->respond(['success' => false, 'message' => 'Error adding station']);
        }
    }

    public function toggleStatus($id) {
        try {
            if(!is_numeric($id) || $id < 1) {
                return $this->fail('Invalid applicant id.', 400);
            }

            // Fetch the secure token from the request
            $secureToken = $this->request->getPost('secureToken');


            // Validate the secure token
            if(!$this->validateSecureToken($id, $secureToken)) {
                return $this->fail('Invalid secure token.', 400);
            }

            // Find the applicant by id
            $station = $this->stations->find($id);

            if(!$station) {
                // Applicant with the given id not found
                return $this->fail('Applicant not found.', 404);
            }

            if($station['status'] !== 'active') {
                // Return an error response or handle it appropriately
                $this->stations->update($id, ['status' => 'active']);
            } else {
                $this->stations->update($id, ['status' => 'inactive']);
            }


            return $this->respond(['success' => true, 'message' => 'Station status toggled successfully']);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => 'Error toggling station status']);
        }
    }

    public function addCourse() {
        $request = service('request');

        // Validate the request data
        $this->validation->setRules([
            'Course_Name' => 'required|string',
            'Course_Description' => 'required|string',
            'Duration' => 'required|integer',
            'Credits' => 'required|integer',
            'status' => 'required|string',
            'Station_Offering.*.Station_ID' => 'required|integer',
        ]);

        if(!$this->validation->withRequest($request)->run()) {
            log_message('debug', print_r($this->validation->getErrors(), true));
            return $this->failValidationErrors($this->validation->getErrors());
        }


        try {
            // Start a database transaction
            $this->db->transStart();

            // Save the course data
            $courseData = [
                'Course_Name' => $request->getPost('Course_Name'),
                'Course_Description' => $request->getPost('Course_Description'),
                'Duration' => $request->getPost('Duration'),
                'Credits' => $request->getPost('Credits'),
                'status' => $request->getPost('status'),
            ];

            $this->courses->insert($courseData);
            $courseId = $this->db->insertID(); // Get the ID of the inserted course

            // Save the associated stations
            $stationsOffering = $request->getPost('Station_Offering');
            foreach($stationsOffering as $stationData) {
                $stationId = $stationData['Station_ID'];
                $this->stationCourses->insert([
                    'Station_ID' => $stationId,
                    'Course_ID' => $courseId,
                ]);
            }

            // Commit the transaction
            $this->db->transCommit();

            return $this->respond(['success' => true, 'message' => 'Course added successfully']);
        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            $this->db->transRollback();

            return $this->respond(['success' => false, 'message' => 'Error adding course']);
        }
    }

    public function editCourse() {
        $request = service('request');

        // Validate the request data
        $this->validation->setRules([
            'Course_ID' => 'required|integer',  // Assuming Course_ID is required for editing
            'Course_Name' => 'required|string',
            'Course_Description' => 'required|string',
            'Duration' => 'required|integer',
            'Credits' => 'required|integer',
            'status' => 'required|string',
            'Station_Offering.*Station_ID' => 'required|integer',
        ]);

        if(!$this->validation->withRequest($request)->run()) {
            return $this->failValidationErrors($this->validation->getErrors());
        }

        // Extract the data
        $courseId = $request->getPost('Course_ID');
        $courseName = $request->getPost('Course_Name');
        $courseDetails = $request->getPost('Course_Description');
        $duration = (int)$request->getPost('Duration');
        $credits = (int)$request->getPost('Credits');
        $status = $request->getPost('status');
        $stationOffering = $request->getPost('Station_Offering');

        // Now you can use the extracted data for further processing, such as updating the database

        try {
            // Start a database transaction
            $this->db->transStart();

            // Update the course data
            $courseData = [
                'Course_Name' => $courseName,
                'Course_Description' => $courseDetails,
                'Duration' => $duration,
                'Credits' => $credits,
                'status' => $status,
            ];

            $this->courses->update($courseId, $courseData);

            // Update the associated stations
            // Note: This assumes you have a separate table for the relationship between courses and stations

            // First, delete existing associations
            $this->stationCourses->where('Course_ID', $courseId)->delete();

            // Now, insert the new associations
            foreach($stationOffering as $stationId) {
                $this->stationCourses->insert([
                    'Course_ID' => $courseId,
                    'Station_ID' => $stationId,
                ]);
            }

            // Commit the transaction
            $this->db->transCommit();

            return $this->respond(['success' => true, 'message' => 'Course updated successfully']);
        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            $this->db->transRollback();

            return $this->respond(['success' => false, 'message' => 'Error updating course']);
        }
    }
    public function toggleCourseStatus($id) {
        try {
            if(!is_numeric($id) || $id < 1) {
                return $this->fail('Invalid course id.', 400);
            }

            // Fetch the secure token from the request
            $secureToken = $this->request->getPost('secureToken');


            // Validate the secure token
            if(!$this->validateSecureToken($id, $secureToken)) {
                return $this->fail('Invalid secure token.', 400);
            }

            // Find the applicant by id
            $station = $this->courses->find($id);

            if(!$station) {
                // Applicant with the given id not found
                return $this->fail('Course not found.', 404);
            }

            if($station['status'] !== 'active') {
                // Return an error response or handle it appropriately
                $this->courses->update($id, ['status' => 'active']);
            } else {
                $this->courses->update($id, ['status' => 'inactive']);
            }


            return $this->respond(['success' => true, 'message' => 'Station status toggled successfully']);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => 'Error toggling station status']);
        }
    }
    public function getStationAdminsWithStation() {
        $data = $this->db->table('stationadmins')
            ->join('stations', 'stationadmins.Station_ID = stations.Station_ID', 'left') // LEFT JOIN with stations table
            ->join('users', 'stationadmins.User_ID = users.User_ID') // INNER JOIN with users table
            ->where('users.IsVerified', 1) // Add condition for IsVerified
            ->get()
            ->getResultArray();

        return $this->respond($data, 200);
    }


    public function toggleStationAdminStatus($id) {
        try {
            if(!is_numeric($id) || $id < 1) {
                return $this->fail('Invalid course id.', 400);
            }

            // Fetch the secure token from the request
            $secureToken = $this->request->getPost('secureToken');


            // Validate the secure token
            if(!$this->validateSecureToken($id, $secureToken)) {
                return $this->fail('Invalid secure token.', 400);
            }

            // Find the applicant by id
            $station = $this->StationAdmin->find($id);

            if(!$station) {
                // Applicant with the given id not found
                return $this->fail('Course not found.', 404);
            }

            if($station['Status'] !== 'active') {
                // Return an error response or handle it appropriately
                $this->StationAdmin->update($id, ['Status' => 'active']);
            } else {
                $this->StationAdmin->update($id, ['Status' => 'inactive']);
            }


            return $this->respond(['success' => true, 'message' => 'Station status toggled successfully']);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => 'Error toggling station status']);
        }
    }
    public function changeAdminStation() {
        try {
            $stationId = $this->request->getPost('Station_ID');
            $stationAdminId = $this->request->getPost('Station_Admin_ID');


            // Get the admin by StationAdmin_ID
            $admin = $this->StationAdmin->find($stationAdminId);

            if(!$admin) {
                return $this->respond(['error' => 'Admin not found'], 404);
            }

            // Update the station for the admin
            $adminData = [
                'Station_ID' => $stationId,
            ];

            $this->StationAdmin->update($stationAdminId, $adminData);

            return $this->respond(['success' => true, 'message' => 'Station updated successfully']);
        } catch (\Exception $e) {
            // Log the exception for debugging and auditing
            log_message('error', 'Exception during changeAdminStation: '.$e->getMessage());

            // Return an error response
            return $this->respond(['error' => 'An error occurred'], 500);
        }
    }
    public function getAdminEditDetails() {
        // Get parameters from the request
        $role = $this->request->getVar('userRole');
        $secureToken = $this->request->getVar('secureToken');
        $userId = $this->request->getVar('userId');

        if(!(int)$role === 1) {
            return $this->failNotFound('Invalid role');
        }

        // Fetch all user details based on the user ID
        $userDetails = $this->mainAdmin->where('User_ID', $userId)->first();

        // Debugging information
        if($userDetails) {
            // Output data for debugging

            // Use $userData as needed in your code
            // For example, you can return it in a response
            return $this->respond($userDetails, 200);
        } else {
            // User not found
            // Handle the case when the user with the specified ID is not found
            return $this->failNotFound('User not found');
        }
    }




    public function updateMainAdminDetails() {

        // Retrieve data from the request
        $userId = $this->request->getPost('userId');
        $userRole = $this->request->getPost('userRole');
        $secureToken = $this->request->getPost('secureToken');



        // Validate user role and secure token (add your own validation logic)
        if($userRole !== '2') {
            return $this->failUnauthorized('Invalid role or secure token');
        }

        // Fetch the MainAdmin record based on user ID
        $mainAdmin = $this->mainAdmin->where('MainAdmin_ID', (int)$userId)->first();

        if(!$mainAdmin) {
            return $this->failNotFound('User not found');
        }

        // Handle the uploaded file
        $profilePicture = $this->request->getFile('profilePicture');



        if($profilePicture && $profilePicture->isValid()) {
            // Generate a new filename and move the file to the destination directory
            $newFileName = $profilePicture->getRandomName();
            $profilePicture->move('public/uploads/applicants/profiles/', $newFileName);

            // Update MainAdmin details including the Profile_Picture field
            $this->mainAdmin->update($userId, [
                'Profile_Picture' => 'public/uploads/applicants/profiles/'.$newFileName,
                'First_Name' => $this->request->getPost('firstName'),
                'Middle_Name' => $this->request->getPost('middleName'),
                'Last_Name' => $this->request->getPost('lastName'),
                'Name_Extension' => $this->request->getPost('nameExtension'),
                'Age' => $this->request->getPost('age'),
                'Sex' => $this->request->getPost('sex'),
                'Address' => $this->request->getPost('address'),
                'Birthplace' => $this->request->getPost('birthplace'),

                'Birthday' => $this->request->getPost('birthday'),
                'Nationality' => $this->request->getPost('nationality'),
                'Religion' => $this->request->getPost('religion'),
                'Admin_PhoneNum' => $this->request->getPost('phoneNumber'),
            ]);
        } else {
            // Update MainAdmin details excluding the Profile_Picture field
            $this->mainAdmin->update($userId, [
                'First_Name' => $this->request->getPost('firstName'),
                'Middle_Name' => $this->request->getPost('middleName'),
                'Last_Name' => $this->request->getPost('lastName'),
                'Name_Extension' => $this->request->getPost('nameExtension'),
                'Age' => $this->request->getPost('age'),
                'Sex' => $this->request->getPost('sex'),
                'Address' => $this->request->getPost('address'),
                'Birthplace' => $this->request->getPost('birthplace'),

                'Birthday' => $this->request->getPost('birthday'),
                'Nationality' => $this->request->getPost('nationality'),
                'Religion' => $this->request->getPost('religion'),
                'Admin_PhoneNum' => $this->request->getPost('phoneNumber'),

                // Add other fields as needed
            ]);

        }

        return $this->respond(['success' => true, 'message' => 'User details updated']);

    }

    // Assuming this is your controller method to update an announcement
    public function updateAnnouncement() {
        // Retrieve data from the form
        $title = $this->request->getPost('title');
        $id = $this->request->getPost('id');
        $content = $this->request->getPost('content');

        // Retrieve the image file if provided
        $image = $this->request->getFile('picture_url');

        // Check if the image is provided and is valid
        if($image !== null && $image->isValid() && !$image->hasMoved()) {
            // Move the uploaded image to a designated folder
            $newName = $image->getRandomName();
            $image->move(ROOTPATH.'public/uploads', $newName);
            $pictureUrl = 'uploads/'.$newName;

            // Update the announcement with the new image
            $this->annoucements->update($id, [
                'title' => $title,
                'content' => $content,
                'picture_url' => $pictureUrl,
                'updated_at' => date('Y-m-d H:i:s'), // Set 'updated_at' to the current timestamp
            ]);
        } else {
            // No new image provided, update without changing the picture_url
            $this->annoucements->update($id, [
                'title' => $title,
                'content' => $content,
                'updated_at' => date('Y-m-d H:i:s'), // Set 'updated_at' to the current timestamp
            ]);
        }

        // Respond with a success message
        return $this->respond(['success' => true, 'message' => 'Announcement updated successfully']);
    }
    public function addAnnouncement() {
        // Retrieve data from the form
        $title = $this->request->getPost('title');
        $content = $this->request->getPost('content');

        // Retrieve the image file if provided
        $image = $this->request->getFile('picture_url');
        $userID = $this->request->getPost('user_ID');

        // Check if the image is provided and is valid
        if($image !== null && $image->isValid() && !$image->hasMoved()) {
            // Move the uploaded image to a designated folder
            $newName = $image->getRandomName();
            $image->move(ROOTPATH.'public/uploads', $newName);
            $pictureUrl = 'uploads/'.$newName;

            // Add a new announcement with the image
            $this->annoucements->insert([
                'announcer_user_id' => $userID,
                'title' => $title,
                'content' => $content,
                'picture_url' => $pictureUrl,
                'created_at' => date('Y-m-d H:i:s'), // Set 'created_at' to the current timestamp
                'updated_at' => date('Y-m-d H:i:s'), // Set 'updated_at' to the current timestamp
            ]);
        } else {
            // No image provided, add a new announcement without 'picture_url'
            $this->annoucements->insert([
                'announcer_user_id' => $userID,
                'title' => $title,
                'content' => $content,
                'created_at' => date('Y-m-d H:i:s'), // Set 'created_at' to the current timestamp
                'updated_at' => date('Y-m-d H:i:s'), // Set 'updated_at' to the current timestamp
            ]);
        }

        // Respond with a success message
        return $this->respond(['success' => true, 'message' => 'Announcement added successfully']);
    }

    public function removeAnnouncement($id) {
        try {
            // Verify the secure token (implement the logic in your backend)
            $secureToken = $this->request->getPost('secureToken');
            $isValidToken = $this->validateSecureToken($id, $secureToken);

            if($isValidToken) {
                // Perform the actual removal of the announcement (use your model)
                $this->annoucements->delete($id);

                // Respond with a success message
                return $this->respond(['success' => true, 'message' => 'Announcement removed successfully']);
            } else {
                // Respond with an error message for invalid token
                return $this->respond(['success' => false, 'message' => 'Invalid secure token'], 403);
            }
        } catch (\Exception $e) {
            // Handle exceptions and respond with an error message
            return $this->respond(['success' => false, 'message' => 'Error removing announcement: '.$e->getMessage()], 500);
        }
    }

    public function getTeacherAssignmentsDetails() {
        $builder = $this->db->table('Users U');
        $builder->select('U.Email AS User_Email, U.Role_ID AS User_Role, T.*, TA.TeachingAssignment_ID, SC.*, S.Station_Name, C.Course_Name');
        $builder->join('Teachers T', 'U.User_ID = T.User_ID');
        $builder->join('TeachingAssignments TA', 'T.Teacher_ID = TA.Teacher_ID');
        $builder->join('StationCourses SC', 'TA.StationCourse_ID = SC.StationCourse_ID');
        $builder->join('Stations S', 'SC.Station_ID = S.Station_ID');
        $builder->join('Courses C', 'SC.Course_ID = C.Course_ID');

        $result = $builder->get()->getResultArray();

        return $this->respond($result); // Assumes you're using the response trait
    }

    public function toggleTeacherStatus($id) {
        try {
            if(!is_numeric($id) || $id < 1) {
                return $this->fail('Invalid applicant id.', 400);
            }

            // Fetch the secure token from the request
            $secureToken = $this->request->getPost('secureToken');


            // Validate the secure token
            if(!$this->validateSecureToken($id, $secureToken)) {
                return $this->fail('Invalid secure token.', 400);
            }

            // Find the applicant by id
            $teachers = $this->teachers->find($id);

            if(!$teachers) {
                // Applicant with the given id not found
                return $this->fail('Applicant not found.', 404);
            }

            if($teachers['Status'] !== 'active') {
                // Return an error response or handle it appropriately
                $this->teachers->update($id, ['Status' => 'active']);
            } else {
                $this->teachers->update($id, ['Status' => 'inactive']);
            }


            return $this->respond(['success' => true, 'message' => 'Station status toggled successfully']);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => 'Error toggling station status']);
        }
    }










}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\SecureTokenTrait;
use DateTime;

class MainAdminController extends BaseController
{

    use SecureTokenTrait;

    public function getApplicants()
    {
        // Get the current year
        $currentYear = date("Y");

        // Query to find all applicants from the current year
        // Replace 'your_date_field' with the actual field name that stores the date
        $data = $this->applicants->where("YEAR(Date_OF_Application)", $currentYear)->findAll();

        if (empty($data)) {
            return $this->failNotFound('No Applicants Found for Current Year');
        }

        return $this->respond($data);
    }
    public function getApplicantsHistory()
    {
        $data = $this->applicants->findAll();
        if (empty($data)) {
            return $this->failNotFound('No Courses Found');
        }
        return $this->respond($data);

    }

    public function approve()
    {
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
                'Age' => (int) $this->request->getVar('Age'),
                'Nationality' => $this->request->getPost('Nationality'),
                'Religion' => $this->request->getPost('Religion'),
                'Address' => $this->request->getPost('Address'),
                'Email' => $this->request->getPost('Email'),
                'Phone_Number' => $this->request->getPost('Phone_Number'),
                'Profile_Picture' => $this->request->getPost('Profile'),
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
                $existingStudent = $this->students->where('User_ID', (int) $existingUser['User_ID'])->first();

                if (!$existingStudent) {
                    // Define user role
                    $userFields = [
                        'Role_ID' => 5,  // Set the role ID as needed
                    ];

                    // Update existing user information
                    $this->users->update($existingUser['User_ID'], $userFields);

                    $userId = $existingUser['User_ID'];

                    //$studID = $this->students->where('User_ID', $userId)->first();
                    // return $this->respond($studID, 200);
                    // Insert student data
                    $studentFields = [
                        'User_ID' => $userId,
                        'Profile_Picture' => $data['Profile_Picture'],
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
                    $studID = $this->students->where("User_ID", (int) $userId)->first();

                    $applicantId = $data['id'];
                    $this->applicants->update($applicantId, ['Status' => 'approved']);


                    $course = $this->courses->where('Course_Name', $data['approvedCourse'])->first();
                    $courseID = $course['Course_ID'];


                    $station = $this->stations->where('Station_Name', $data['approvedStation'])->first();
                    $stationID = $station['Station_ID'];

                    $enrollmentFields = [
                        'Stud_ID' => (int) $studID['Stud_ID'],
                        'Course_ID' => (int) $courseID,
                        'Station_ID' => (int) $stationID,
                    ];

                    // return $this->respond($enrollmentFields, 200);
                    $r = $this->enrollments->insert($enrollmentFields);

                    if (!$this->sendApprovalEmail($existingUser['Email'], $data['approvedCourse'], $data['approvedStation'])) {
                        return $this->fail('Failed to send approval email.', 500);
                    }

                } else {

                    return $this->fail('User is already a student.');
                }
            } else {

                return $this->fail('User does not exist.');
            }

            $this->db->transComplete();
            return $this->respond(true, 200);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage());

            return $this->fail('An error occurred while processing the request.');
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

            if (!$this->sendRejectionEmail($applicant['Email'])) {
                // Handle the case where email sending fails
                return $this->fail('Failed to send rejection email.', 500);
            }

            return $this->respond('Applicant rejected successfully.', 200);
        } catch (\Exception $e) {
            // Handle exceptions here
            return $this->fail('An error occurred.', 500);
        }
    }


    public function editStation()
    {
        $request = service('request');

        // Validate the request data
        $this->validation->setRules([
            'Station_ID' => 'required|integer',
            'Station_Name' => 'required|string',
            'Location' => 'required|string',
            'status' => 'required|string',
            'Courses_Offered.*.Course_ID' => 'required|integer', // Assuming Course_ID is the ID
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
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
            foreach ($coursesOffered as $courseData) {
                $courseId = $courseData['Course_ID']; // Assuming Course_ID is the ID

                if (!in_array($courseId, $existingCourseIds)) {
                    // Course is not linked to the station, so add it
                    $courseData['Station_ID'] = $stationId;
                    $this->stationCourses->insert($courseData);
                }
            }

            // Check for removed courses
            foreach ($existingCourses as $existingCourse) {
                $existingCourseId = $existingCourse['Course_ID'];
                $existingStationCourseId = $existingCourse['StationCourse_ID'];

                // Check if the existing course is not present in the received form data
                if (!in_array($existingCourseId, array_column($coursesOffered, 'Course_ID'))) {
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

    public function addStation()
    {
        $request = service('request');

        // Validate the request data
        $this->validation->setRules([
            'Station_Name' => 'required|string',
            'Location' => 'required|string',
            'status' => 'required|string',
            'Courses_Offered.*.Course_ID' => 'required|integer',
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
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
            foreach ($coursesOffered as $courseData) {
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

    public function toggleStatus($id)
    {
        try {
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
            $station = $this->stations->find($id);

            if (!$station) {
                // Applicant with the given id not found
                return $this->fail('Applicant not found.', 404);
            }

            if ($station['status'] !== 'active') {
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

    public function addCourse()
    {
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

        if (!$this->validation->withRequest($request)->run()) {
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
            foreach ($stationsOffering as $stationData) {
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

    public function editCourse()
    {
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

        if (!$this->validation->withRequest($request)->run()) {
            return $this->failValidationErrors($this->validation->getErrors());
        }

        // Extract the data
        $courseId = $request->getPost('Course_ID');
        $courseName = $request->getPost('Course_Name');
        $courseDetails = $request->getPost('Course_Description');
        $duration = (int) $request->getPost('Duration');
        $credits = (int) $request->getPost('Credits');
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
            foreach ($stationOffering as $stationId) {
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
    public function toggleCourseStatus($id)
    {
        try {
            if (!is_numeric($id) || $id < 1) {
                return $this->fail('Invalid course id.', 400);
            }

            // Fetch the secure token from the request
            $secureToken = $this->request->getPost('secureToken');


            // Validate the secure token
            if (!$this->validateSecureToken($id, $secureToken)) {
                return $this->fail('Invalid secure token.', 400);
            }

            // Find the applicant by id
            $station = $this->courses->find($id);

            if (!$station) {
                // Applicant with the given id not found
                return $this->fail('Course not found.', 404);
            }

            if ($station['status'] !== 'active') {
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
    public function getStationAdminsWithStation()
    {
        $data = $this->db->table('stationadmins')
            ->join('stations', 'stationadmins.Station_ID = stations.Station_ID', 'left') // LEFT JOIN with stations table
            ->join('users', 'stationadmins.User_ID = users.User_ID') // INNER JOIN with users table
            ->where('users.IsVerified', 1) // Add condition for IsVerified
            ->get()
            ->getResultArray();

        return $this->respond($data, 200);
    }


    public function toggleStationAdminStatus($id)
    {
        try {
            if (!is_numeric($id) || $id < 1) {
                return $this->fail('Invalid course id.', 400);
            }

            // Fetch the secure token from the request
            $secureToken = $this->request->getPost('secureToken');


            // Validate the secure token
            if (!$this->validateSecureToken($id, $secureToken)) {
                return $this->fail('Invalid secure token.', 400);
            }

            // Find the applicant by id
            $station = $this->StationAdmin->find($id);

            if (!$station) {
                // Applicant with the given id not found
                return $this->fail('Course not found.', 404);
            }

            if ($station['Status'] !== 'active') {
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
    public function changeAdminStation()
    {
        try {
            $stationId = $this->request->getPost('Station_ID');
            $stationAdminId = $this->request->getPost('Station_Admin_ID');


            // Get the admin by StationAdmin_ID
            $admin = $this->StationAdmin->find($stationAdminId);

            if (!$admin) {
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
            log_message('error', 'Exception during changeAdminStation: ' . $e->getMessage());

            // Return an error response
            return $this->respond(['error' => 'An error occurred'], 500);
        }
    }
    public function changeTeacherStation()
    {
        try {
            $stationId = $this->request->getPost('Station_ID');
            $teacherId = $this->request->getPost('Station_Admin_ID'); // Change to Teacher_ID

            // Get the teacher by Teacher_ID
            $teacher = $this->teacherAssignments->where('Teacher_ID', (int) $teacherId)->first();

            if (!$teacher) {
                return $this->respond(['error' => 'Teacher not found'], 404);
            }

            // Update the station for the teacher
            $teacherData = [
                'station_id' => $stationId, // Ensure that 'station_id' is the correct column name in your table
            ];

            // Perform the update
            $this->teacherAssignments->set($teacherData)
                ->where('Teacher_ID', (int) $teacherId)
                ->update();

            return $this->respond(['success' => true, 'message' => 'Station updated successfully']);
        } catch (\Exception $e) {
            // Log the exception for debugging and auditing
            log_message('error', 'Exception during changeTeacherStation: ' . $e->getMessage());

            // Return an error response
            return $this->respond(['error' => 'An error occurred'], 500);
        }
    }

    public function getAdminEditDetails()
    {
        // Get parameters from the request
        $role = $this->request->getVar('userRole');
        $secureToken = $this->request->getVar('secureToken');
        $userId = $this->request->getVar('userId');

        if (!(int) $role === 1) {
            return $this->failNotFound('Invalid role');
        }

        // Fetch all user details based on the user ID
        $userDetails = $this->mainAdmin->where('User_ID', $userId)->first();

        // Debugging information
        if ($userDetails) {
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




    public function updateMainAdminDetails()
    {

        // Retrieve data from the request
        $userId = $this->request->getPost('userId');
        $userRole = $this->request->getPost('userRole');
        $secureToken = $this->request->getPost('secureToken');



        // Validate user role and secure token (add your own validation logic)
        if ($userRole !== '2') {
            return $this->failUnauthorized('Invalid role or secure token');
        }

        // Fetch the MainAdmin record based on user ID
        $mainAdmin = $this->mainAdmin->where('MainAdmin_ID', (int) $userId)->first();

        if (!$mainAdmin) {
            return $this->failNotFound('User not found');
        }

        // Handle the uploaded file
        $profilePicture = $this->request->getFile('profilePicture');



        if ($profilePicture && $profilePicture->isValid()) {
            // Generate a new filename and move the file to the destination directory
            $newFileName = $profilePicture->getRandomName();
            $profilePicture->move('public/uploads/applicants/profiles/', $newFileName);

            // Update MainAdmin details including the Profile_Picture field
            $this->mainAdmin->update($userId, [
                'Profile_Picture' => 'public/uploads/applicants/profiles/' . $newFileName,
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
    public function updateAnnouncement()
    {
        helper(['form', 'url']);

        $announcementId = $this->request->getPost('id');
        $title = $this->request->getPost('title');
        $content = $this->request->getPost('content');
        $pictureUrl = $this->request->getFile('picture_url');

        if ($pictureUrl && $pictureUrl->isValid() && !$pictureUrl->hasMoved()) {
            $newName = $pictureUrl->getRandomName();
            $pictureUrl->move(ROOTPATH . 'public/uploads', $newName);
            $pictureUrl = 'uploads/' . $newName;
        } else {
            $pictureUrl = null; // Handle case where no new image is uploaded
        }
        $datetime = new DateTime();
        $updated_at = $datetime->format('Y-m-d H:i:s');
        $announcementData = [
            'title' => $title,
            'content' => $content,
            'picture_url' => $pictureUrl,
            'updated_at' => $updated_at,
        ];

        $announcementModel = new \App\Models\AnnouncementModel();
        $announcementModel->update($announcementId, $announcementData);

        $stationIds = $this->request->getVar('Station_ID') ?? [];
        $this->updateStationRelations($announcementId, $stationIds);

        return $this->respond(['success' => true, 'message' => 'Announcement updated successfully']);
    }

    private function updateStationRelations($announcementId, $stationIds)
    {
        $stationModel = new \App\Models\StationAnnouncementModel();

        $stationModel->where('announcement_id', $announcementId)->delete();
        foreach ($stationIds as $stationId) {
            $stationModel->insert([
                'announcement_id' => $announcementId,
                'station_id' => $stationId

            ]);
        }
    }


    public function addAnnouncement()
    {
        helper(['form', 'url']);

        $title = $this->request->getPost('title');
        $content = $this->request->getPost('content');
        $userID = $this->request->getPost('user_ID');
        $stationIDs = $this->request->getPost('Station_ID'); // Ensure this is an array

        $pictureUrl = $this->request->getFile('picture_url');

        // Process the image file if provided and valid
        if ($pictureUrl && !$pictureUrl->hasMoved()) {
            $newName = $pictureUrl->getRandomName();
            $pictureUrl->move(ROOTPATH . 'public/uploads', $newName);
            $pictureUrl = 'uploads/' . $newName;
        }

        $announcementData = [
            'announcer_user_id' => $userID,
            'title' => $title,
            'content' => $content,
            'picture_url' => $pictureUrl ?? null,
            // 'created_at' and 'updated_at' will be automatically handled if you set $useTimestamps in your model
        ];

        $announcementModel = new \App\Models\AnnouncementModel();
        $announcementID = $announcementModel->insert($announcementData);

        $tokens = $this->getFCMTokens();
        if (!empty($tokens)) {
            $this->sendPushNotification($tokens, $title, $content, $pictureUrl);
        }

        // If $stationIDs is not an array or is empty, treat the announcement as a general announcement
        if (is_array($stationIDs) && !empty($stationIDs)) {
            $stationAnnouncementModel = new \App\Models\StationAnnouncementModel(); // Assuming you have a model for the junction table

            foreach ($stationIDs as $stationID) {
                $stationAnnouncementModel->insert([
                    'announcement_id' => $announcementID,
                    'station_id' => $stationID
                ]);
            }
        }

        return $this->respondCreated(['success' => true, 'message' => 'Announcement added successfully']);
    }
    protected $firebaseServerKey = 'AAAAGdrwYnc:APA91bGXw5cv3F3unpb56ySKuWglwbUrCNnYbsyZ7RxDCHLTHSIapFcRmcNoOpWIYIBY928el1PEN39VOyK_kqu1T13Pq78GqvrTg8T8EDmAxFY2Iv0S_B9E9Jf-TDL2RTaSZyXlx-IX';
    private function sendFirebaseNotification($title, $message)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        // Retrieve FCM tokens from your database
        $tokens = $this->getFCMTokens(); // Implement this method to get tokens

        $fields = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $message,
                // Optionally add other notification properties here
            ],
            // Optionally add 'data' payload for additional data with the notification
        ];

        $headers = [
            'Authorization: key=' . $this->firebaseServerKey, // Your server key
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $response = curl_exec($ch);
        curl_close($ch);

        // Optionally handle the response from FCM
    }

    private function getFCMTokens()
    {
        $userModel = new \App\Models\UserModel(); // Replace with your user model
        $users = $userModel->findAll();
        $tokens = [];

        foreach ($users as $user) {
            if (!empty($user['fcm_token'])) {
                $tokens[] = $user['fcm_token'];
            }
        }

        return $tokens;
    }
    public function sendPushNotification($tokens, $title, $body, $imageUrl)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = [
            'Authorization: key=' . $this->firebaseServerKey,
            'Content-Type: application/json'
        ];

        $fields = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'image' => "http://backend.test/" . $imageUrl,
                'click_action' => 'action_url' // Optional: action URL on click
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }



    public function removeAnnouncement($id)
    {
        try {
            // Verify the secure token (implement the logic in your backend)
            $secureToken = $this->request->getPost('secureToken');
            $isValidToken = $this->validateSecureToken($id, $secureToken);

            if ($isValidToken) {
                // Begin a transaction
                $this->db->transStart();

                // Remove corresponding entries from station_announcements
                $stationAnnouncementModel = new \App\Models\StationAnnouncementModel();
                $stationAnnouncementModel->where('announcement_id', $id)->delete();

                // Remove the announcement
                $this->annoucements->delete($id);

                // Commit the transaction
                $this->db->transComplete();

                if ($this->db->transStatus() === FALSE) {
                    throw new \Exception('Transaction failed');
                }

                // Respond with a success message
                return $this->respond(['success' => true, 'message' => 'Announcement removed successfully']);
            } else {
                // Respond with an error message for invalid token
                return $this->respond(['success' => false, 'message' => 'Invalid secure token'], 403);
            }
        } catch (\Exception $e) {
            // Handle exceptions and respond with an error message
            return $this->respond(['success' => false, 'message' => 'Error removing announcement: ' . $e->getMessage()], 500);
        }
    }


    public function getTeacherAssignmentsDetails()
    {
        $builder = $this->db->table('Users U');
        $builder->select('U.Email AS User_Email, U.Role_ID AS User_Role, T.*, TA.TeachingAssignment_ID, TA.course_id, TA.station_id, S.Station_Name, C.Course_Name');
        $builder->join('Teachers T', 'U.User_ID = T.User_ID');
        $builder->join('TeachingAssignments TA', 'T.Teacher_ID = TA.Teacher_ID');
        $builder->join('Stations S', 'TA.station_id = S.Station_ID');
        $builder->join('Courses C', 'TA.course_id = C.Course_ID');

        $result = $builder->get()->getResultArray();

        return $this->respond($result);
    }


    public function toggleTeacherStatus($id)
    {
        try {
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
            $teachers = $this->teachers->find($id);

            if (!$teachers) {
                // Applicant with the given id not found
                return $this->fail('Applicant not found.', 404);
            }

            if ($teachers['Status'] !== 'active') {
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
    public function changeTeacherCourse()
    {
        try {
            $Course_ID = $this->request->getPost('Course_ID');
            $Teacher_ID = $this->request->getPost('Teacher_ID'); // Change to Teacher_ID

            // Get the teacher by Teacher_ID
            $teacher = $this->teacherAssignments->where('Teacher_ID', $Teacher_ID)->first();

            if (!$teacher) {
                return $this->respond(['error' => 'Teacher not found'], 404);
            }

            // Update the station for the teacher
            $teacherData = [
                'course_id' => $Course_ID, // Change to station_id

            ];


            $this->teacherAssignments->set($teacherData)->where('Teacher_ID', $Teacher_ID)->update();

            return $this->respond(['success' => true, 'message' => 'Station updated successfully']);
        } catch (\Exception $e) {
            // Log the exception for debugging and auditing
            log_message('error', 'Exception during changeTeacherStation: ' . $e->getMessage());

            // Return an error response
            return $this->respond(['error' => 'An error occurred'], 500);
        }
    }









}

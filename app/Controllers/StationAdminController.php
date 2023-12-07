<?php

namespace App\Controllers;


use App\Controllers\BaseController;


class StationAdminController extends BaseController {

    public function getStationAdminEditDetails() {
        // Get parameters from the request
        $role = $this->request->getVar('userRole');
        $secureToken = $this->request->getVar('secureToken');
        $userId = $this->request->getVar('userId');


        if(!(int)$role === 3) {
            return $this->failNotFound('Invalid role');
        }

        // Fetch all user details based on the user ID
        $userDetails = $this->StationAdmin->where('User_ID', $userId)->first();

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

    public function updateStationAdminDetails() {

        // Retrieve data from the request
        $userId = $this->request->getPost('userId');
        $userRole = $this->request->getPost('userRole');
        $secureToken = $this->request->getPost('secureToken');



        // Validate user role and secure token (add your own validation logic)
        if($userRole !== '3') {
            return $this->failUnauthorized('Invalid role or secure token');
        }

        // Fetch the stationAdmin record based on user ID
        $stationAdmin = $this->StationAdmin->where('StationAdmin_ID', (int)$userId)->first();
        //   return $this->respond($stationAdmin);
        if(!$stationAdmin) {
            return $this->failNotFound('User not found');
        }

        // Handle the uploaded file
        $profilePicture = $this->request->getFile('profilePicture');




        if($profilePicture && $profilePicture->isValid()) {
            // Generate a new filename and move the file to the destination directory
            $newFileName = $profilePicture->getRandomName();
            $profilePicture->move('public/uploads/applicants/profiles/', $newFileName);

            // Update stationAdmin details including the Profile_Picture field
            $this->StationAdmin->update($userId, [
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
            // Update stationAdmin details excluding the Profile_Picture field
            $this->StationAdmin->update($userId, [
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

    public function getStationDetailsperStation() {
        try {
            // Get stationAdminId from the request
            $stationAdminId = $this->request->getPost('id');

            // Retrieve the station admin details based on the logged station admin's ID
            $stationAdminDetails = $this->StationAdmin->where('User_ID', $stationAdminId)->first();

            // Check if station admin details were found
            if(!$stationAdminDetails) {
                return $this->failNotFound('Station admin details not found');
            }

            // Get the Station_ID associated with the logged station admin
            $stationId = $stationAdminDetails['Station_ID'];

            // Retrieve station details based on the Station_ID
            $stationDetails = $this->stations->find($stationId);

            // Check if station details were found
            if(!$stationDetails) {
                return $this->failNotFound('Station details not found');
            }

            // Retrieve courses offered by the station
            $courses = $this->getCoursesByStation($stationId);

            // Add the courses to the stationDetails array
            $stationDetails['Courses_Offered'] = $courses;

            // Respond with the fetched station details including courses
            return $this->respond($stationDetails);
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Error fetching station details: '.$e->getMessage());

            // Respond with an error
            return $this->failServerError('Internal Server Error');
        }
    }

    // Helper method to get courses offered by a station
    private function getCoursesByStation($stationId) {
        $courses = $this->db->table('courses')
            ->select('courses.*, stations.Station_ID, stations.Station_Name, stations.Location, stations.status as sstatus, stations.created_at as screated_at, stations.status_updated_at as sstatus_updated_at')
            ->join('stationcourses', 'courses.Course_ID = stationcourses.Course_ID')
            ->join('stations', 'stationcourses.Station_ID = stations.Station_ID')
            ->where('stations.Station_ID', $stationId)
            ->get()
            ->getResult();

        // Organize the data as needed
        $data = [];

        foreach($courses as $course) {
            // Check if the course is already in $data
            $existingCourse = array_filter($data, function ($item) use ($course) {
                return $item['Course_ID'] === $course->Course_ID;
            });

            // If the course is not in $data, add it
            if(empty($existingCourse)) {
                $courseData = [
                    'Course_ID' => $course->Course_ID,
                    'Course_Name' => $course->Course_Name,
                    'Course_Description' => $course->Course_Description,
                    'Duration' => $course->Duration,
                    'Credits' => $course->Credits,
                    'status' => $course->status,
                    'created_at' => $course->created_at,
                    'status_updated_at' => $course->status_updated_at,
                    'Stations_Offering' => [
                        [
                            'Station_ID' => $course->Station_ID,
                            'Station_Name' => $course->Station_Name,
                            'Location' => $course->Location,
                            'status' => $course->sstatus,
                            'created_at' => $course->screated_at,
                            'status_updated_at' => $course->sstatus_updated_at,
                        ],
                    ],
                ];

                $data[] = $courseData;
            } else {
                // If the course is already in $data, add the station to its 'Stations_Offering' array
                $key = key($existingCourse);
                $data[$key]['Stations_Offering'][] = [
                    'Station_ID' => $course->Station_ID,
                    'Station_Name' => $course->Station_Name,
                    'Location' => $course->Location,
                    'status' => $course->sstatus,
                    'created_at' => $course->screated_at,
                    'status_updated_at' => $course->sstatus_updated_at,
                ];
            }
        }

        return $data;
    }
    // public function saveStationChanges() {
    //     $request = service('request');

    //     // Validate the request data
    //     $this->validation->setRules([
    //         'id' => 'required|integer',
    //         'Station_Name' => 'required|string',
    //         'Location' => 'required|string',
    //         'status' => 'required|string',
    //         'Courses_Offered.*.Course_ID' => 'required|integer', // Assuming Course_ID is the ID
    //     ]);

    //     if(!$this->validation->withRequest($this->request)->run()) {
    //         return $this->failValidationErrors($this->validation->getErrors());
    //     }

    //     // Extract the data
    //     $stationId = $request->getPost('id');
    //     $stationName = $request->getPost('Station_Name');
    //     $location = $request->getPost('Location');
    //     $status = $request->getPost('status');
    //     $coursesOffered = $request->getPost('Courses_Offered');

    //     // Start a database transaction
    //     $this->db->transStart();

    //     try {
    //         // Save the station data
    //         $stationData = [
    //             'Station_Name' => $stationName,
    //             'Location' => $location,
    //             'status' => $status,
    //         ];

    //         $this->stations->update($stationId, $stationData);

    //         // Get the existing courses associated with the station
    //         $existingCourses = $this->stationCourses->where('Station_ID', $stationId)->findAll();
    //         // return $this->respond($existingCourses);
    //         // Extract the Course_ID values from existing courses
    //         $existingCourseIds = array_column($existingCourses, 'Course_ID');


    //         // Compare the existing courses with the courses received in the form
    //         foreach($coursesOffered as $courseData) {
    //             $courseId = $courseData['Course_ID']; // Assuming Course_ID is the ID

    //             if(!in_array($courseId, $existingCourseIds)) {
    //                 // Course is not linked to the station, so add it
    //                 $courseData['Station_ID'] = $stationId;
    //                 $this->stationCourses->insert($courseData);
    //             }
    //         }

    //         // Check for removed courses
    //         foreach($existingCourses as $existingCourse) {
    //             $existingCourseId = $existingCourse['Course_ID'];
    //             $existingStationCourseId = $existingCourse['StationCourse_ID'];

    //             // Check if the existing course is not present in the received form data
    //             if(!in_array($existingCourseId, array_column($coursesOffered, 'Course_ID'))) {
    //                 // Course is in the database but not in the form, so remove it
    //                 $this->stationCourses->delete(['StationCourse_ID' => $existingStationCourseId]);
    //             }
    //         }

    //         // Commit the transaction
    //         $this->db->transCommit();

    //         return $this->respond(['success' => true, 'message' => 'Station edited successfully']);
    //     } catch (\Exception $e) {
    //         // An error occurred, rollback the transaction
    //         $this->db->transRollback();

    //         return $this->respond(['success' => false, 'message' => 'Error editing station']);
    //     }
    // }







}

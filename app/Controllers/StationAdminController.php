<?php

namespace App\Controllers;


use App\Controllers\BaseController;


class StationAdminController extends BaseController
{

    public function getStationAdminEditDetails()
    {
        // Get parameters from the request
        $role = $this->request->getVar('userRole');
        $secureToken = $this->request->getVar('secureToken');
        $userId = $this->request->getVar('userId');


        if (!(int) $role === 3) {
            return $this->failNotFound('Invalid role');
        }

        // Fetch all user details based on the user ID
        $userDetails = $this->StationAdmin->where('User_ID', $userId)->first();

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

    public function updateStationAdminDetails()
    {

        // Retrieve data from the request
        $userId = $this->request->getPost('userId');
        $userRole = $this->request->getPost('userRole');
        $secureToken = $this->request->getPost('secureToken');



        // Validate user role and secure token (add your own validation logic)
        if ($userRole !== '3') {
            return $this->failUnauthorized('Invalid role or secure token');
        }

        // Fetch the stationAdmin record based on user ID
        $stationAdmin = $this->StationAdmin->where('StationAdmin_ID', (int) $userId)->first();
        //   return $this->respond($stationAdmin);
        if (!$stationAdmin) {
            return $this->failNotFound('User not found');
        }

        // Handle the uploaded file
        $profilePicture = $this->request->getFile('profilePicture');




        if ($profilePicture && $profilePicture->isValid()) {
            // Generate a new filename and move the file to the destination directory
            $newFileName = $profilePicture->getRandomName();
            $profilePicture->move('public/uploads/applicants/profiles/', $newFileName);

            // Update stationAdmin details including the Profile_Picture field
            $this->StationAdmin->update($userId, [
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

    public function getStationDetailsperStation()
    {
        try {
            // Get stationAdminId from the request
            $stationAdminId = $this->request->getPost('id');

            // Retrieve the station admin details based on the logged station admin's ID
            $stationAdminDetails = $this->StationAdmin->where('User_ID', $stationAdminId)->first();

            // Check if station admin details were found
            if (!$stationAdminDetails) {
                return $this->failNotFound('Station admin details not found');
            }

            // Get the Station_ID associated with the logged station admin
            $stationId = $stationAdminDetails['Station_ID'];

            // Retrieve station details based on the Station_ID
            $stationDetails = $this->stations->find($stationId);

            // Check if station details were found
            if (!$stationDetails) {
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
            log_message('error', 'Error fetching station details: ' . $e->getMessage());

            // Respond with an error
            return $this->failServerError('Internal Server Error');
        }
    }

    // Helper method to get courses offered by a station
    private function getCoursesByStation($stationId)
    {
        $courses = $this->db->table('courses')
            ->select('courses.*, stations.Station_ID, stations.Station_Name, stations.Location, stations.status as sstatus, stations.created_at as screated_at, stations.status_updated_at as sstatus_updated_at')
            ->join('stationcourses', 'courses.Course_ID = stationcourses.Course_ID')
            ->join('stations', 'stationcourses.Station_ID = stations.Station_ID')
            ->where('stations.Station_ID', $stationId)
            ->get()
            ->getResult();

        // Organize the data as needed
        $data = [];

        foreach ($courses as $course) {
            // Check if the course is already in $data
            $existingCourse = array_filter($data, function ($item) use ($course) {
                return $item['Course_ID'] === $course->Course_ID;
            });

            // If the course is not in $data, add it
            if (empty($existingCourse)) {
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
    public function getTeacherAssignmentsDetailsperStation()
    {
        try {
            // Get station_id from the POST data
            $stationId = $this->request->getVar('station_id');

            // Validate station_id if needed

            // Fetch teacher assignments based on the station_id
            // $stationId = 12; // Set the correct station_id value
            $data = $this->db->table('teachers')
                ->join('teachingassignments', 'teachingassignments.Teacher_ID = teachers.Teacher_ID')
                ->join('users', 'teachers.User_ID = users.User_ID')
                ->join('courses', 'teachingassignments.course_id = courses.Course_ID')
                ->where('users.IsVerified', 1) // Add condition for IsVerified
                ->where('teachingassignments.station_id', (int) $stationId) // Add condition for Station_ID
                ->get()
                ->getResultArray();

            return $this->respond($data, 200);

        } catch (\Exception $e) {
            // Handle exceptions
            return $this->failServerError('Internal Server Error');
        }
    }


    public function getStationCoursesWithStudentsAndTeachers()
    {
        try {
            // Get the Station_ID associated with the logged station admin
//$stationId = $this->getLoggedInStationId(); // You need to implement the logic to get the logged-in station ID

            $builder = $this->db->table('stationcourses');

            $courses = $builder
                ->select('courses.*, teachers.Teacher_ID, teachers.First_Name AS Teacher_First_Name, teachers.Last_Name AS Teacher_Last_Name, students.Stud_ID, users_students.First_Name AS Student_First_Name, users_students.Last_Name AS Student_Last_Name')
                ->join('courses', 'stationcourses.Course_ID = courses.Course_ID')
                ->join('teachingassignments', 'stationcourses.StationCourse_ID = teachingassignments.StationCourse_ID', 'left')
                ->join('teachers', 'teachingassignments.Teacher_ID = teachers.Teacher_ID', 'left')
                ->join('users AS teachers', 'teachers.User_ID = teachers.User_ID', 'left')
                ->join('enrollments', 'stationcourses.StationCourse_ID = enrollments.StationCourse_ID', 'left')
                ->join('students', 'enrollments.Stud_ID = students.Stud_ID', 'left')
                ->join('users AS users_students', 'students.User_ID = users_students.User_ID', 'left')
                ->where('stationcourses.Station_ID', 11)
                ->get()
                ->getResult();

            if (empty($courses)) {
                return $this->failNotFound('No Courses Found');
            }

            // Organize the data as needed
            $data = [];

            foreach ($courses as $course) {
                $courseData = [
                    'Course_ID' => $course->Course_ID,
                    'Course_Name' => $course->Course_Name,
                    'Course_Description' => $course->Course_Description,
                    'Duration' => $course->Duration,
                    'Credits' => $course->Credits,
                    'Teachers' => [],
                    'Students' => [],
                ];

                if ($course->Teacher_ID) {
                    $courseData['Teachers'][] = [
                        'Teacher_ID' => $course->Teacher_ID,
                        'Teacher_First_Name' => $course->Teacher_First_Name,
                        'Teacher_Last_Name' => $course->Teacher_Last_Name,
                    ];
                }

                if ($course->Stud_ID) {
                    $courseData['Students'][] = [
                        'Stud_ID' => $course->Stud_ID,
                        'Student_First_Name' => $course->Student_First_Name,
                        'Student_Last_Name' => $course->Student_Last_Name,
                    ];
                }

                $data[] = $courseData;
            }

            return $this->respond($data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching station courses: ' . $e->getMessage());
            return $this->failServerError('Internal Server Error');
        }
    }

    public function getEnrollmentsByCourse()
    {
        try {
            $stationId = $this->request->getVar('station_id');

            // Fetch enrollments grouped by course with left join
            $enrollmentsByCourse = $this->db->table('enrollments as e')
                ->select('
                    e.Enrollment_ID,
                    e.Stud_ID as Enrollment_Stud_ID,
                    e.Course_ID as Enrollment_Course_ID,
                    e.Station_ID as Enrollment_Station_ID,
                    e.Enrollment_Date,
                    e.Enrollment_Status,
                    s.Stud_ID,
                    s.User_ID as Student_User_ID,
                    s.Profile as Student_Profile,
                    s.First_Name as Student_First_Name,
                    s.Middle_Name as Student_Middle_Name,
                    s.Last_Name as Student_Last_Name,
                    s.Name_Extension as Student_Name_Extension,
                    s.Age as Student_Age,
                    s.Sex as Student_Sex,
                    s.Address as Student_Address,
                    s.Birthday as Student_Birthday,
                    s.Birthplace as Student_Birthplace,
                    s.Status as Student_Status,
                    s.Nationality as Student_Nationality,
                    s.Religion as Student_Religion,
                    s.Stud_PhoneNum,
                    ta.TeachingAssignment_ID,
                    ta.Teacher_ID as TeachingAssignment_Teacher_ID,
                    ta.course_id as TeachingAssignment_Course_ID,
                    ta.station_id as TeachingAssignment_Station_ID,
                    t.Teacher_ID,
                    t.User_ID as Teacher_User_ID,
                    t.Profile_Picture as Teacher_Profile_Picture,
                    t.First_Name as Teacher_First_Name,
                    t.Middle_Name as Teacher_Middle_Name,
                    t.Last_Name as Teacher_Last_Name,
                    t.Name_Extension as Teacher_Name_Extension,
                    t.Age as Teacher_Age,
                    t.Sex as Teacher_Sex,
                    t.Address as Teacher_Address,
                    t.Birthday as Teacher_Birthday,
                    t.Birthplace as Teacher_Birthplace,
                    t.Status as Teacher_Status,
                    t.Nationality as Teacher_Nationality,
                    t.Religion as Teacher_Religion,
                    t.Teacher_PhoneNum,
                    c.Course_ID as Course_ID,
                    c.Course_Name,
                    c.Course_Description,
                    c.Duration,
                    c.Credits,
                    c.status as Course_Status,
                    c.created_at as Course_Created_At,
                    c.status_updated_at as Course_Status_Updated_At'
                )
                ->join('students as s', 'e.Stud_ID = s.Stud_ID', 'left')
                ->join('teachingassignments as ta', 'e.Course_ID = ta.course_id', 'left')
                ->join('teachers as t', 'ta.Teacher_ID = t.Teacher_ID', 'left')
                ->join('courses as c', 'e.Course_ID = c.Course_ID', 'left')
                ->where('e.Station_ID', (int) $stationId)
                ->get()
                ->getResult();

            // Organize data into the desired format
            $formattedData = [];

            foreach ($enrollmentsByCourse as $enrollment) {
                // Check if the course already exists in the formatted data
                $existingCourse = array_search($enrollment->Enrollment_Course_ID, array_column($formattedData, 'course_details.Course_ID'));

                $courseDetails = [
                    "Course_ID" => $enrollment->Course_ID,
                    "Course_Name" => $enrollment->Course_Name,
                    "Course_Description" => $enrollment->Course_Description,
                    "Duration" => $enrollment->Duration,
                    "Credits" => $enrollment->Credits,
                    "status" => $enrollment->Course_Status,
                    "created_at" => $enrollment->Course_Created_At,
                    "status_updated_at" => $enrollment->Course_Status_Updated_At
                ];

                $teacherDetails = [
                    "Teacher_ID" => $enrollment->Teacher_ID,
                    "User_ID" => $enrollment->Teacher_User_ID,
                    "Profile_Picture" => $enrollment->Teacher_Profile_Picture,
                    "First_Name" => $enrollment->Teacher_First_Name,
                    "Middle_Name" => $enrollment->Teacher_Middle_Name,
                    "Last_Name" => $enrollment->Teacher_Last_Name,
                    "Name_Extension" => $enrollment->Teacher_Name_Extension,
                    "Age" => $enrollment->Teacher_Age,
                    "Sex" => $enrollment->Teacher_Sex,
                    "Address" => $enrollment->Teacher_Address,
                    "Birthday" => $enrollment->Teacher_Birthday,
                    "Birthplace" => $enrollment->Teacher_Birthplace,
                    "Status" => $enrollment->Teacher_Status,
                    "Nationality" => $enrollment->Teacher_Nationality,
                    "Religion" => $enrollment->Teacher_Religion,
                    "Teacher_PhoneNum" => $enrollment->Teacher_PhoneNum
                ];

                $studentDetails = [
                    "Enrollment_ID" => $enrollment->Enrollment_ID,
                    "Stud_ID" => $enrollment->Enrollment_Stud_ID,
                    "Station_ID" => $enrollment->Enrollment_Station_ID,
                    "Enrollment_Date" => $enrollment->Enrollment_Date,
                    "Enrollment_Status" => $enrollment->Enrollment_Status,
                    "User_ID" => $enrollment->Student_User_ID,
                    "Profile" => $enrollment->Student_Profile,
                    "First_Name" => $enrollment->Student_First_Name,
                    "Middle_Name" => $enrollment->Student_Middle_Name,
                    "Last_Name" => $enrollment->Student_Last_Name,
                    "Name_Extension" => $enrollment->Student_Name_Extension,
                    "Age" => $enrollment->Student_Age,
                    "Sex" => $enrollment->Student_Sex,
                    "Address" => $enrollment->Student_Address,
                    "Birthday" => $enrollment->Student_Birthday,
                    "Birthplace" => $enrollment->Student_Birthplace,
                    "Status" => $enrollment->Student_Status,
                    "Nationality" => $enrollment->Student_Nationality,
                    "Religion" => $enrollment->Student_Religion,
                    "Stud_PhoneNum" => $enrollment->Stud_PhoneNum
                ];

                if ($existingCourse !== false) {
                    // Course already exists, append teacher and student details
                    $formattedData[$existingCourse]['teachers'][] = $teacherDetails;
                    $formattedData[$existingCourse]['enrolled_students'][] = $studentDetails;
                } else {
                    // Course doesn't exist, create a new entry
                    $formattedData[] = [
                        "course_details" => $courseDetails,
                        "teachers" => [$teacherDetails],
                        "enrolled_students" => [$studentDetails]
                    ];
                }
            }

            return $this->respond($formattedData);
        } catch (\Exception $e) {
            // Handle exceptions
            return $this->fail('Failed to fetch enrollments by course', 500);
        }
    }

    public function getTeacherSchedule()
    {
        try {
            $request = $this->request->getJSON(); // Assuming the data is sent as JSON

            // Get the teacherId from the request
            $teacherId = (int) $request->teacherId;


            // Fetch the schedule data for the specified teacher
            $scheduleData = $this->dailyschedule->where('Teacher_ID', $teacherId)->findAll();

            // Respond with the schedule data
            return $this->respond($scheduleData);
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Error fetching teacher schedule: ' . $e->getMessage());

            // Respond with a server error
            return $this->failServerError('An unexpected error occurred.');
        }
    }

    public function saveSchedule()
    {
        try {
            // Assuming the data is sent as form data
            $teacherId = $this->request->getPost('teacherId');
            $title = $this->request->getPost('title');
            $description = $this->request->getPost('description');
            $daysOfWeek = $this->request->getPost('daysOfWeek');
            $monthFrom = $this->request->getPost('monthFrom');
            $monthTo = $this->request->getPost('monthTo');
            $timeFrom = $this->request->getPost('timeFrom');
            $timeTo = $this->request->getPost('timeTo');
            // Add other fields as needed...

            // Convert string values to appropriate types if necessary
            $data = [
                'Teacher_ID' => (int) $teacherId,
                'Title' => $title,
                'Description' => $description,
                'DaysOfWeek' => $daysOfWeek,
                'MonthFrom' => $this->adjustMonthFrom($monthFrom), // Use a function to adjust MonthFrom
                'MonthTo' => $this->adjustMonthTo($monthTo), // Use a function to adjust MonthTo
                'TimeFrom' => $timeFrom,
                'TimeTo' => $timeTo,
                // Add other fields as needed...
            ];

            // Insert data into the database
            $this->dailyschedule->insert($data);

            return $this->respondCreated(['message' => 'Schedule saved successfully']);
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Error saving schedule: ' . $e->getMessage());

            // Respond with a server error
            return $this->failServerError('An unexpected error occurred.');
        }
    }

    // Function to adjust MonthFrom to the first day of the month
    private function adjustMonthFrom($monthFrom)
    {
        $firstDayOfMonth = date('Y-m-01', strtotime($monthFrom));
        return $firstDayOfMonth;
    }

    // Function to adjust MonthTo to the last day of the month
    private function adjustMonthTo($monthTo)
    {
        $lastDayOfMonth = date('Y-m-t', strtotime($monthTo));
        return $lastDayOfMonth;
    }















}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;



class MainController extends BaseController {

    // public function test() {
    //     $mainAdmin = $this->mainAdmin->where('User_ID', 84)->first();
    //     var_dump($mainAdmin);

    // }
    public function getCoursesWithStation() {
        $statusFilter = $this->request->getGet('status');

        $queryBuilder = $this->db->table('courses');

        // Check if a status filter is provided
        if($statusFilter) {
            // Assuming 'status' is a field in the 'courses' table
            $queryBuilder->where('courses.status', $statusFilter)->where('stations.status', $statusFilter);
        }

        $courses = $queryBuilder
            ->select('courses.*, stations.Station_ID, stations.Station_Name, stations.Location, stations.status as station_status, stations.created_at as station_created_at, stations.status_updated_at as station_status_updated_at')
            ->join('stationcourses', 'courses.Course_ID = stationcourses.Course_ID')
            ->join('stations', 'stationcourses.Station_ID = stations.Station_ID')
            ->get()
            ->getResult();

        if(empty($courses)) {
            return $this->failNotFound('No Courses Found');
        }

        // Organize the data as needed
        $organizedData = [];

        foreach($courses as $course) {
            // Check if the course is already in $organizedData
            $existingCourse = array_filter($organizedData, function ($item) use ($course) {
                return $item['Course_ID'] === $course->Course_ID;
            });

            // If the course is not in $organizedData, add it
            if(empty($existingCourse)) {
                $courseData = [
                    'Course_ID' => $course->Course_ID,
                    'Course_Name' => $course->Course_Name,
                    'Course_Description' => $course->Course_Description,
                    'Duration' => $course->Duration,
                    'Credits' => $course->Credits,
                    'Status' => $course->status,
                    'Created_At' => $course->created_at,
                    'Status_Updated_At' => $course->status_updated_at,
                    'Stations_Offering' => [
                        [
                            'Station_ID' => $course->Station_ID,
                            'Station_Name' => $course->Station_Name,
                            'Location' => $course->Location,
                            'Status' => $course->station_status,
                            'Created_At' => $course->station_created_at,
                            'Status_Updated_At' => $course->station_status_updated_at,
                        ],
                    ],
                ];

                $organizedData[] = $courseData;
            } else {
                // If the course is already in $organizedData, add the station to its 'Stations_Offering' array
                $key = key($existingCourse);
                $organizedData[$key]['Stations_Offering'][] = [
                    'Station_ID' => $course->Station_ID,
                    'Station_Name' => $course->Station_Name,
                    'Location' => $course->Location,
                    'Status' => $course->station_status,
                    'Created_At' => $course->station_created_at,
                    'Status_Updated_At' => $course->station_status_updated_at,
                ];
            }
        }

        return $this->respond($organizedData);
    }

    // Example CodeIgniter controller method


    public function getStationswithCourses() {
        $statusFilter = $this->request->getGet('status');
        $queryBuilder = $this->db->table('stations');

        if($statusFilter) {
            // Assuming 'status' is a field in the 'stations' table
            $queryBuilder->where('stations.status', $statusFilter)->where('courses.status', $statusFilter);
        }

        $stations = $queryBuilder
            ->select('stations.*, courses.Course_ID, courses.Course_Name, courses.Course_Description, courses.Duration, courses.Credits, courses.status as course_status, courses.created_at as course_created_at, courses.status_updated_at as course_status_updated_at')
            ->join('stationcourses', 'stations.Station_ID = stationcourses.Station_ID')
            ->join('courses', 'stationcourses.Course_ID = courses.Course_ID')
            ->get()
            ->getResult();

        if(empty($stations)) {
            return $this->failNotFound('No Stations Found');
        }

        // Organize the data as needed
        $organizedData = [];

        foreach($stations as $station) {
            // Check if the station is already in $organizedData
            $existingStation = array_filter($organizedData, function ($item) use ($station) {
                return $item['Station_ID'] === $station->Station_ID;
            });

            // If the station is not in $organizedData, add it
            if(empty($existingStation)) {
                $stationData = [
                    'Station_ID' => $station->Station_ID,
                    'Station_Name' => $station->Station_Name,
                    'Location' => $station->Location,
                    'Status' => $station->status,
                    'Created_At' => $station->created_at,
                    'Status_Updated_At' => $station->status_updated_at,
                    'Courses_Offered' => [
                        [
                            'Course_ID' => $station->Course_ID,
                            'Course_Name' => $station->Course_Name,
                            'Course_Description' => $station->Course_Description,
                            'Duration' => $station->Duration,
                            'Credits' => $station->Credits,
                            'Status' => $station->course_status,
                            'Created_At' => $station->course_created_at,
                            'Status_Updated_At' => $station->course_status_updated_at,
                        ],
                    ],
                ];

                $organizedData[] = $stationData;
            } else {
                // If the station is already in $organizedData, add the course to its 'Courses_Offered' array
                $key = key($existingStation);
                $organizedData[$key]['Courses_Offered'][] = [
                    'Course_ID' => $station->Course_ID,
                    'Course_Name' => $station->Course_Name,
                    'Course_Description' => $station->Course_Description,
                    'Duration' => $station->Duration,
                    'Credits' => $station->Credits,
                    'Status' => $station->course_status,
                    'Created_At' => $station->course_created_at,
                    'Status_Updated_At' => $station->course_status_updated_at,
                ];
            }
        }

        return $this->respond($organizedData);
    }





    public function getStations() {
        try {
            // Fetch all active stations
            $activeStations = $this->stations->where('status', 'active')->findAll();
            return $this->respond($activeStations);
        } catch (\Exception $e) {
            return $this->fail('Failed to fetch stations', 500);
        }
    }

    public function getCourses() {
        try {
            // Fetch all active courses
            $activeCourses = $this->courses->where('status', 'active')->findAll();
            return $this->respond($activeCourses);
        } catch (\Exception $e) {
            return $this->fail('Failed to fetch courses', 500);
        }
    }


    public function getRoles() {
        try {
            // Get the userRole from the request data
            $userRole = (int)$this->request->getPost('userRole');

            // Initialize roles array
            $roles = [];

            if($userRole !== 1) { // Assuming 1 is the Role_ID for Admin
                // Fetch all roles
                $roles = $this->roles->findAll();
            }

            // Your logic to filter roles based on $userRole

            // Return the roles in the response
            return $this->respond($roles);
        } catch (\Exception $e) {
            // Handle exceptions
            return $this->fail('Failed to fetch roles', 500);
        }
    }


    public function getAnnouncements() {
        try {
            // Fetch announcements along with mainadmins details using a left join
            $announcements = $this->db->table('announcements')
                ->select('announcements.*, mainadmins.MainAdmin_ID, mainadmins.First_Name, mainadmins.Last_Name, mainadmins.Profile_Picture')
                ->join('mainadmins', 'announcements.announcer_user_id = mainadmins.User_ID', 'left')
                ->orderBy('announcements.created_at', 'DESC')
                ->get()
                ->getResultArray();

            // Check if announcements are found
            if(empty($announcements)) {
                return $this->failNotFound('No announcements found');
            }

            return $this->respond($announcements, 200);
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch announcements: '.$e->getMessage());
            return $this->failServerError('Failed to fetch announcements');
        }
    }





}

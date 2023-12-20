<?php

namespace App\Controllers;

use App\Controllers\BaseController;



class MainController extends BaseController
{

    // public function test() {
    //     $mainAdmin = $this->mainAdmin->where('User_ID', 84)->first();
    //     var_dump($mainAdmin);

    // }
    public function getCoursesInStation($stationId = null)
    {
        $status = $this->request->getGet('status');

        $builder = $this->db->table('courses');

        // Check if a status filter is provided
        if ($status) {
            // Assuming 'status' is a field in the 'courses' table
            $builder->where('courses.status', $status)->where('stations.status', $status);
        }

        // Add a WHERE clause for the station_id if it's provided
        if ($stationId) {
            $builder->where('stationcourses.Station_ID', $stationId);
        }

        $courses = $builder
            ->select('courses.*, stations.Station_ID, stations.Station_Name, stations.Location, stations.status as sstatus, stations.created_at as screated_at, stations.status_updated_at as sstatus_updated_at')
            ->join('stationcourses', 'courses.Course_ID = stationcourses.Course_ID')
            ->join('stations', 'stationcourses.Station_ID = stations.Station_ID')
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

        return $this->respond($data);
    }


    public function getCourses()
    {
        $status = $this->request->getGet('status');

        $builder = $this->db->table('courses');

        // Check if a status filter is provided
        if ($status) {
            // Assuming 'status' is a field in the 'courses' table
            $builder->where('courses.status', $status)->where('stations.status', $status);
        }

        $courses = $builder
            ->select('courses.*, stations.Station_ID, stations.Station_Name, stations.Location,stations.status as sstatus,stations.created_at as screated_at,stations.status_updated_at as sstatus_updated_at')
            ->join('stationcourses', 'courses.Course_ID = stationcourses.Course_ID')
            ->join('stations', 'stationcourses.Station_ID = stations.Station_ID')
            ->get()
            ->getResult();

        if (empty($courses)) {
            return $this->failNotFound('No Courses Found');
        }

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

        return $this->respond($data);
    }
    // Example CodeIgniter controller method


    public function getStations()
    {
        $status = $this->request->getGet('status');
        $builder = $this->db->table('stations');

        if ($status) {
            // Assuming 'status' is a field in the 'courses' table
            $builder->where('stations.status', $status)->where('courses.status', $status);
        }

        $stations = $builder
            ->select('stations.*, courses.Course_ID, courses.Course_Name, courses.Course_Description, courses.Duration, courses.Credits,courses.status as cstatus,courses.created_at as ccreated_at,courses.status_updated_at as cstatus_updated_at')
            ->join('stationcourses', 'stations.Station_ID = stationcourses.Station_ID')
            ->join('courses', 'stationcourses.Course_ID = courses.course_ID')
            ->get()
            ->getResult();



        if (empty($stations)) {
            return $this->failNotFound('No Stations Found');
        }

        // Organize the data as needed
        $data = [];

        foreach ($stations as $station) {
            // Check if the station is already in $data
            $existingStation = array_filter($data, function ($item) use ($station) {
                return $item['Station_ID'] === $station->Station_ID;
            });

            // If the station is not in $data, add it
            if (empty($existingStation)) {
                $stationData = [
                    'Station_ID' => $station->Station_ID,
                    'Station_Name' => $station->Station_Name,
                    'Location' => $station->Location,
                    'status' => $station->status,
                    'created_at' => $station->created_at,
                    'status_updated_at' => $station->status_updated_at,
                    'Courses_Offered' => [
                        [
                            'Course_ID' => $station->Course_ID,
                            'Course_Name' => $station->Course_Name,
                            'Course_Description' => $station->Course_Description,
                            'Duration' => $station->Duration,
                            'Credits' => $station->Credits,
                            'status' => $station->cstatus,
                            'created_at' => $station->ccreated_at,
                            'status_updated_at' => $station->cstatus_updated_at,
                        ],
                    ],
                ];

                $data[] = $stationData;
            } else {
                // If the station is already in $data, add the course to its 'Courses_Offered' array
                $key = key($existingStation);
                $data[$key]['Courses_Offered'][] = [
                    'Course_ID' => $station->Course_ID,
                    'Course_Name' => $station->Course_Name,
                    'Course_Description' => $station->Course_Description,
                    'Duration' => $station->Duration,
                    'Credits' => $station->Credits,
                    'status' => $station->status,
                    'created_at' => $station->created_at,
                    'status_updated_at' => $station->status_updated_at,
                ];
            }
        }

        return $this->respond($data);
    }





    public function getStation()
    {
        // Fetch all stations
        $stations = $this->stations->where("status", 'active')->findAll();
        return $this->respond($stations);
    }


    public function getCourse()
    {
        // Fetch all courses
        $courses = $this->courses->where("status", 'active')->findAll();
        return $this->respond($courses);
    }

    public function getRoles()
    {
        try {
            // Get the userRole from the request data
            $userRole = (int) $this->request->getPost('userRole');

            // Assuming your roles table has a structure like this
            if ($userRole !== 1 && $userRole !== 6) { // Assuming 1 is the Role_ID for Admin
                $roles = $this->roles->whereNotIn('Role_ID', [6])->findAll();


            }

            // Your logic to filter roles based on $userRole

            // Return the roles in the response
            return $this->respond($roles);
        } catch (\Exception $e) {
            // Handle exceptions
            return $this->fail('Failed to fetch roles', 500);
        }
    }


    // Assuming you have the necessary models set up for TeacherModel, StudentModel, StationAdminModel,
// TeachingAssignmentModel, EnrollmentModel, and AnnouncementModel.

    public function getAnnouncements($role, $userId)
    {
        try {
            $announcements = [];

            (int) $userId;
            if ($role === '4') {
                // Fetch announcements for the teacher
                $teacherModel = $this->teachers;
                $teacher = $teacherModel->where('User_ID', $userId)->first();

                if ($teacher) {
                    $teachingAssignmentModel = $this->teacherAssignments;
                    $teachingAssignments = $teachingAssignmentModel->where('Teacher_ID', (int) $teacher['Teacher_ID'])->first();


                    $announcements = $this->fetchAnnouncementsForStation((int) $teachingAssignments['station_id']);

                }
            } elseif ($role === '5') {
                // Fetch announcements for the student
                $studentModel = $this->students;
                $student = $studentModel->where('User_ID', $userId)->first();
                if ($student) {
                    $enrollmentModel = $this->enrollments;
                    $enrollments = $enrollmentModel->where('Stud_ID', (int) $student['Stud_ID'])->first();

                    $announcements = $this->fetchAnnouncementsForStation((int) $enrollments['Station_ID']);

                }
            } elseif ($role === '3') {
                // Fetch announcements for the station admin directly based on their Station_ID
                $stationAdminModel = $this->StationAdmin;
                $stationAdmin = $stationAdminModel->where('User_ID', $userId)->first();
                if ($stationAdmin) {
                    $announcements = $this->fetchAnnouncementsForStation($stationAdmin['Station_ID']);
                }
            } elseif ($role === '2') {
                // Fetch announcements for the user role 2 (all announcements)
                $announcements = $this->fetchAnnouncementsForStation(null);

            }

            return $this->response->setJSON($announcements);
        } catch (\Exception $e) {
            // Handle exceptions
            return $this->response->setJSON(['error' => 'Failed to fetch announcements']);
        }
    }

    private function fetchAnnouncementsForStation($stationId)
    {
        $builder = $this->db->table('announcements');
        $builder->select('announcements.*, mainadmins.MainAdmin_ID, mainadmins.First_Name, mainadmins.Last_Name, mainadmins.Profile_Picture');

        // Subquery to get associated station IDs as a comma-separated string
        $builder->select('(SELECT GROUP_CONCAT(station_id) FROM announcement_stations WHERE announcement_id = announcements.id) as Station_IDs', false);

        // Join with mainadmins table
        $builder->join('mainadmins', 'announcements.announcer_user_id = mainadmins.User_ID', 'left');

        // Include announcements that have no station specified (general announcements)
        if ($stationId !== null) {
            $builder->join('announcement_stations', 'announcements.id = announcement_stations.announcement_id', 'left');
            $builder->groupStart();
            $builder->where('announcement_stations.station_id', $stationId);
            $builder->orWhere('announcement_stations.station_id', null);
            $builder->groupEnd();
        }

        // Order by creation date
        $builder->orderBy('announcements.created_at', 'DESC');

        $announcements = $builder->get()->getResultArray();

        // Convert Station_IDs string to an array
        foreach ($announcements as &$announcement) {
            if ($announcement['Station_IDs'] !== null) {
                $announcement['Station_IDs'] = explode(',', $announcement['Station_IDs']);
            } else {
                $announcement['Station_IDs'] = [];
            }
        }

        return $announcements;
    }







}

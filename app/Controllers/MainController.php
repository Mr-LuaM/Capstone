<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;


class MainController extends ResourceController
{
    use ResponseTrait;

    private $courses;
    private $stationCourses;
    private $stations;
    private $users;

    public function __construct()
    {
        $this->courses = new \App\Models\CoursesModel;
        $this->stationCourses = new \App\Models\StationCoursesModel;
        $this->stations = new \App\Models\StationsModel;
        $this->users = new \App\Models\UserModel;
    }

    public function getCourses()
    {
        $courses = $this->courses->findAll();

        if (empty($courses)) {
            return $this->failNotFound('No Courses Found');
        }

        // Create an array to store the courses and their associated stations
        $data = [];

        foreach ($courses as $course) {
            // Fetch station IDs associated with the course
            $stationCourseRecords = $this->stationCourses->where('Course_ID', $course['Course_ID'])->findAll();

            $stations = [];

            foreach ($stationCourseRecords as $stationCourseRecord) {
                // Fetch station details using the Station_ID
                $station = $this->stations->find($stationCourseRecord['Station_ID']);

                if ($station) {
                    // Structure the station data as desired
                    $stationData = [
                        'Station_ID' => $station['Station_ID'],
                        'Station_Name' => $station['Station_Name'],
                        'Location' => $station['Location'],
                    ];

                    $stations[] = $stationData;
                }
            }

            // Structure the data for the course
            $courseData = [
                'Course_ID' => $course['Course_ID'],
                'Course_Name' => $course['Course_Name'],
                'Course_Description' => $course['Course_Description'],
                'Duration' => $course['Duration'],
                'Credits' => $course['Credits'],
                'Stations_Offering' => $stations,
            ];

            $data[] = $courseData;
        }

        return $this->respond($data);
    }

    public function getStations()
    {
        // Fetch all stations
        $stations = $this->stations->findAll();

        if (empty($stations)) {
            return $this->failNotFound('No Stations Found');
        }

        // Create an array to store station data and associated courses
        $data = [];

        foreach ($stations as $station) {
            // Fetch course IDs associated with the station
            $stationCourseRecords = $this->stationCourses->where('Station_ID', $station['Station_ID'])->findAll();

            $courses = [];

            foreach ($stationCourseRecords as $stationCourseRecord) {
                // Fetch course details using the Course_ID
                $course = $this->courses->find($stationCourseRecord['Course_ID']);

                if ($course) {
                    // Structure the course data as desired
                    $courseData = [
                        'Course_ID' => $course['Course_ID'],
                        'Course_Name' => $course['Course_Name'],
                        'Course_Description' => $course['Course_Description'],
                        'Duration' => $course['Duration'],
                        'Credits' => $course['Credits'],
                    ];

                    $courses[] = $courseData;
                }
            }

            // Structure the data for the station
            $stationData = [
                'Station_ID' => $station['Station_ID'],
                'Station_Name' => $station['Station_Name'],
                'Location' => $station['Location'],
                'Courses_Offered' => $courses,
            ];

            $data[] = $stationData;
        }

        return $this->respond($data);
    }
    public function getStation()
    {
        // Fetch all stations
        $stations = $this->stations->findAll();
        return $this->respond($stations);
    }

    public function getUsers()
    {
        // Fetch all stations
        $users = $this->users->findAll();
        return $this->respond($users);
    }

}

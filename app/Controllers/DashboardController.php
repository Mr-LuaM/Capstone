<?php

namespace App\Controllers;

use App\Traits\SecureTokenTrait;
use App\Controllers\BaseController;

class DashboardController extends BaseController {
    use SecureTokenTrait;
    public function getEnrollmentDetails() {
        try {

            $enrollmentData = $this->enrollments
                ->select('enrollments.Enrollment_ID, enrollments.Stud_ID, enrollments.Course_ID, enrollments.Station_ID, enrollments.Stud_Name, enrollments.Enrollment_Date, enrollments.Enrollment_Status, courses.Course_Name, stations.Station_Name')
                ->join('courses', 'courses.Course_ID = enrollments.Course_ID')
                ->join('stations', 'stations.Station_ID = enrollments.Station_ID')
                ->findAll();

            // Respond with JSON
            return $this->respond($enrollmentData);
        } catch (\Exception $e) {
            // Handle errors
            return $this->failServerError('Error fetching dashboard data: '.$e->getMessage());
        }
    }
}

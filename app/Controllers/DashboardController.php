<?php

namespace App\Controllers;

use App\Traits\SecureTokenTrait;
use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    use SecureTokenTrait;
    public function getEnrollmentDetails()
    {
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
            return $this->failServerError('Error fetching dashboard data: ' . $e->getMessage());
        }
    }


    public function getUserStatistics()
    {

        $totalUsers = $this->users->countAllResults();

        $currentYearUsers = $this->users->where('YEAR(RegistrationDate)', date('Y'))->countAllResults();
        $previousYearUsers = $this->users->where('YEAR(RegistrationDate)', date('Y') - 1)->countAllResults();

        $percentageIncrease = 0;
        if ($previousYearUsers > 0) {
            $percentageIncrease = (($currentYearUsers - $previousYearUsers) / $previousYearUsers) * 100;
        }

        $data = [
            'totalUsers' => $totalUsers,
            'percentageIncrease' => round($percentageIncrease, 2)
        ];
        return $this->response->setJSON($data);
    }
    public function getStationAdminStatistics()
    {
        $model = $this->users; // Assuming you have a UsersModel

        $currentYear = date('Y');
        $lastYear = (int) date('Y') - 1;

        $currentYearAdmins = $model->where('Role_ID', 3)
            ->where('YEAR(RegistrationDate)', $currentYear)
            ->countAllResults();

        $lastYearAdmins = $model->where('Role_ID', 3)
            ->where('YEAR(RegistrationDate)', $lastYear)
            ->countAllResults();

        $percentageIncrease = 0;
        if ($lastYearAdmins > 0) {
            $percentageIncrease = (($currentYearAdmins - $lastYearAdmins) / $lastYearAdmins) * 100;
        }

        $data = [
            'totalStationAdmins' => $currentYearAdmins,
            'percentageIncrease' => round($percentageIncrease, 2)
        ];

        return $this->response->setJSON($data);
    }
    public function getTeacherStatistics()
    {
        $model = $this->users; // Your model for the 'users' table

        $currentYear = date('Y');
        $lastYear = (int) date('Y') - 1;

        $currentYearTeachers = $model->where('Role_ID', 4)
            ->where('YEAR(RegistrationDate)', $currentYear)
            ->countAllResults();

        $lastYearTeachers = $model->where('Role_ID', 4)
            ->where('YEAR(RegistrationDate)', $lastYear)
            ->countAllResults();

        $percentageIncrease = 0;
        if ($lastYearTeachers > 0) {
            $percentageIncrease = (($currentYearTeachers - $lastYearTeachers) / $lastYearTeachers) * 100;
        }

        $data = [
            'totalTeachers' => $currentYearTeachers,
            'percentageIncrease' => round($percentageIncrease, 2)
        ];

        return $this->response->setJSON($data);
    }

    public function getStudentStatistics()
    {
        $model = $this->users; // Your model for the 'users' table
        // Assuming you have a UsersModel

        $currentYear = date('Y');
        $lastYear = (int) date('Y') - 1;

        $currentYearStudents = $model->where('Role_ID', 5)
            ->where('YEAR(RegistrationDate)', $currentYear)
            ->countAllResults();

        $lastYearStudents = $model->where('Role_ID', 5)
            ->where('YEAR(RegistrationDate)', $lastYear)
            ->countAllResults();

        $percentageIncrease = 0;
        if ($lastYearStudents > 0) {
            $percentageIncrease = (($currentYearStudents - $lastYearStudents) / $lastYearStudents) * 100;
        }

        $data = [
            'totalStudents' => $currentYearStudents,
            'percentageIncrease' => round($percentageIncrease, 2)
        ];

        return $this->response->setJSON($data);
    }
    public function getEnrollmentTrends()
    {
        // Initialize your ApplicantsModel
        $applicantsModel = $this->applicants;

        // Modify this query based on your database structure
        $trendsData = $applicantsModel->select('YEAR(Date_Of_Application) as Year, 
            SUM(CASE WHEN Status = "Approved" THEN 1 ELSE 0 END) as Approved, 
            SUM(CASE WHEN Status = "Rejected" THEN 1 ELSE 0 END) as Rejected')
            ->groupBy('YEAR(Date_Of_Application)')
            ->orderBy('Year')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($trendsData);
    }


    public function getCourseTrends()
    {
        // Create an array to store the counts for each course
        $courseCounts = [];

        // Define the columns to count
        $courseColumns = ['Course1', 'Course2', 'Course3'];

        // Loop through each column
        foreach ($courseColumns as $column) {
            $query = $this->db->table('applicants');
            $query->select("$column as Course, COUNT(*) as Count");
            $query->groupBy("$column");
            $result = $query->get()->getResultArray();

            // Merge the counts into the courseCounts array
            foreach ($result as $row) {
                $course = $row['Course'];
                $count = $row['Count'];

                // Check if the course is not null or empty
                if (!empty($course)) {
                    // If the course already exists in the array, add the count, otherwise, initialize it
                    if (array_key_exists($course, $courseCounts)) {
                        $courseCounts[$course] += $count;
                    } else {
                        $courseCounts[$course] = $count;
                    }
                }
            }
        }

        // Prepare the data for charting
        $chartData = [];
        foreach ($courseCounts as $course => $count) {
            $chartData[] = [
                'Course' => $course,
                'Enrollments' => $count,
            ];
        }

        return $this->response->setJSON($chartData);
    }





}

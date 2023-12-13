<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use DateTime;

class StudentController extends BaseController
{
    public function getstudentEditDetails()
    {
        // Get parameters from the request
        $role = $this->request->getVar('userRole');
        $secureToken = $this->request->getVar('secureToken');
        $userId = $this->request->getVar('userId');

        if (!(int) $role === 1) {
            return $this->failNotFound('Invalid role');
        }

        // Fetch all user details based on the user ID
        $userDetails = $this->students->where('User_ID', $userId)->first();

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

    public function updateStudentDetails()
    {

        // Retrieve data from the request
        $userId = $this->request->getPost('userId');
        $userRole = $this->request->getPost('userRole');
        $secureToken = $this->request->getPost('secureToken');



        // Validate user role and secure token (add your own validation logic)
        if ($userRole !== '5') {
            return $this->failUnauthorized('Invalid role or secure token');
        }

        // Fetch the MainAdmin record based on user ID
        $students = $this->students->where('Stud_ID', (int) $userId)->first();

        if (!$students) {
            return $this->failNotFound('User not found');
        }

        // Handle the uploaded file
        $profilePicture = $this->request->getFile('profilePicture');



        if ($profilePicture && $profilePicture->isValid()) {
            // Generate a new filename and move the file to the destination directory
            $newFileName = $profilePicture->getRandomName();
            $profilePicture->move('public/uploads/applicants/profiles/', $newFileName);

            // Update MainAdmin details including the Profile_Picture field
            $this->students->update($userId, [
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
                'Stud_PhoneNum' => $this->request->getPost('phoneNumber'),
            ]);
        } else {
            // Update MainAdmin details excluding the Profile_Picture field
            $this->students->update($userId, [
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
                'Stud_PhoneNum' => $this->request->getPost('phoneNumber'),

                // Add other fields as needed
            ]);

        }

        return $this->respond(['success' => true, 'message' => 'User details updated']);

    }

    public function getStudentSchedule()
    {
        $userData = $this->request->getJSON();
        $userId = $userData->user_id;

        $studentModel = $this->students; // Replace with your actual student model
        $enrollmentModel = $this->enrollments;
        $teachingAssignmentModel = $this->teacherAssignments;
        $scheduleModel = $this->dailyschedule; // Assuming you have a schedule model

        // Find the student ID based on the user ID
        $student = $studentModel->where('User_ID', $userId)->first();
        if (!$student) {
            return $this->response->setJSON(['error' => 'Student not found']);
        }
        $studentId = $student['Stud_ID'];

        // Get the course IDs for which the student is enrolled
        $enrollments = $enrollmentModel
            ->where('Stud_ID', $studentId)
            ->where('Enrollment_Status', 'ongoing')
            ->findAll();

        $courseIds = array_column($enrollments, 'Course_ID');

        // Get the teacher IDs for these courses
        $assignments = $teachingAssignmentModel
            ->where('course_id', $courseIds)
            ->findAll();

        $teacherIds = array_column($assignments, 'Teacher_ID');

        // Get the schedules for these teachers
        $schedules = $scheduleModel
            ->where('Teacher_ID', $teacherIds)
            ->where('Title', 'Lecture')
            ->findAll();

        return $this->response->setJSON($schedules);
    }

    public function getStudentProgressByUserId()
    {
        $userData = $this->request->getJSON();
        $userId = $userData->user_id;

        $userModel = $this->users;
        $enrollmentModel = $this->enrollments;
        $courseModel = $this->courses;

        // Check if the user exists
        $user = $userModel->find($userId);
        if (!$user) {
            return $this->failNotFound('User not found');
        }

        // Check if the user is associated with a student
        $student = $this->students->where('User_ID', $userId)->first();
        if (!$student) {
            return $this->failNotFound('Student not found');
        }
        $studentId = $student['Stud_ID'];

        // Check if the student has an ongoing enrollment
        $enrollment = $enrollmentModel
            ->where('Stud_ID', $studentId)
            ->where('Enrollment_Status', 'ongoing')
            ->first();
        if (!$enrollment) {
            return $this->failNotFound('Enrollment not found');
        }

        // Check if the enrolled course exists
        // Fetch course details
        $course = $courseModel->find($enrollment['Course_ID']);
        if (!$course) {
            return $this->failNotFound('Course not found');
        }

        // Check if the course duration is 0
        if ($course['Duration'] == 0) {
            // Handle this case as you see fit. For example:
            $progress = 100; // If you consider the course to be instantly complete

        } else {
            // Calculate progress normally
            $endDate = (new DateTime($enrollment['Enrollment_Date']))->modify('+' . $course['Duration'] . ' months');
            $currentDate = new DateTime();
            $startDate = new DateTime($enrollment['Enrollment_Date']);
            $totalDuration = $startDate->diff($endDate)->days;
            $elapsedDuration = $startDate->diff($currentDate)->days;

            $progress = min(($elapsedDuration / $totalDuration) * 100, 100); // Progress as a percentage
        }

        return $this->respond(['progress' => $progress]);

    }




}

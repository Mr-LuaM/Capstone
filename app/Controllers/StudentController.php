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
        $student = $studentModel->where('User_ID', (int) $userId)->first();
        if (!$student) {
            return $this->response->setJSON(['error' => 'Student not found']);
        }

        // Get the current year
        $currentYear = date("Y");

        // Get the enrollments for the current year
        $enrollments = $enrollmentModel
            ->where('Stud_ID', $student['Stud_ID'])
            ->where('Enrollment_Status', 'ongoing')
            ->like('Enrollment_Date', $currentYear, 'after') // Filter by current year
            ->findAll();

        if (empty($enrollments)) {
            return $this->response->setJSON(['error' => 'No enrollments found for the current year']);
        }

        $schedules = [];
        foreach ($enrollments as $enrollment) {
            // Get the teaching assignment for the course
            $assignment = $teachingAssignmentModel
                ->where('course_id', (int) $enrollment['Course_ID'])
                ->first();

            if ($assignment) {
                // Get the schedule for the teacher
                $teacherSchedules = $scheduleModel
                    ->where('Teacher_ID', (int) $assignment['Teacher_ID'])
                    ->where('Title', 'Lecture') // Assuming you want to filter by lectures
                    ->findAll();

                foreach ($teacherSchedules as $schedule) {
                    $schedules[] = $schedule;
                }
            }
        }

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

        // Fetch course details
        $course = $courseModel->find($enrollment['Course_ID']);
        if (!$course) {
            return $this->failNotFound('Course not found');
        }

        // Calculate progress
        $startDate = new DateTime($enrollment['Enrollment_Date']);
        $endDate = (clone $startDate)->modify('+' . $course['Duration'] . ' months');
        $currentDate = new DateTime();

        $totalDuration = $startDate->diff($endDate)->format("%a"); // Total duration in days
        $elapsedInterval = $startDate->diff($currentDate);
        $elapsedDurationInHours = $elapsedInterval->days * 24 + $elapsedInterval->h;
        $elapsedDuration = $elapsedDurationInHours / 24; // Convert hours to days

        if ($totalDuration > 0) {
            $progress = ($elapsedDuration / $totalDuration) * 100;
            $progress = min($progress, 100); // cap at 100%
        } else {
            $progress = 100;
        }

        return $this->respond(['progress' => $progress]);
    }

    public function getStudentData()
    {
        (int) $userId = $this->request->getVar('user_id');

        // Step 1: Get Student ID from User ID
        $student = $this->db->table('students')
            ->where('User_ID', $userId)
            ->get()
            ->getRow();
        if (!$student) {
            return $this->failNotFound('Student not found');
        }

        // Step 2: Fetch Enrollments
        $enrollments = $this->db->table('enrollments')
            ->where('Stud_ID', $student->Stud_ID)
            ->get()
            ->getResult();

        // Step 3: Get Course Details and Grades
        $totalCredits = 0;
        foreach ($enrollments as &$enrollment) {
            $course = $this->db->table('courses')
                ->where('Course_ID', $enrollment->Course_ID)
                ->get()
                ->getRow();

            $enrollment->course = $course;

            // Add course credits to total if the course is passed
            if ($enrollment->Enrollment_Status == 'passed') {
                $totalCredits += $course->Credits;
            }

            // Fetch grade for each enrollment (if stored separately)
            $grade = $this->db->table('grades')
                ->where('Enrollment_ID', $enrollment->Enrollment_ID)
                ->get()
                ->getRow();
            $enrollment->grade = $grade ? $grade->Grade : null;
        }

        // Prepare and return the response
        $data = [
            'enrollments' => $enrollments,
            'totalCredits' => $totalCredits
        ];

        return $this->respond($data);
    }

    public function getStudentExams()
    {
        $userId = $this->request->getVar('userId');

        // Find student ID from users table
        $student = $this->students->where('User_ID', (int) $userId)->first();
        if (!$student) {
            return $this->failNotFound('Student not found.');
        }
        $studId = $student['Stud_ID'];

        // Get ongoing enrollments for the current year
        $currentYear = date('Y');
        $enrollments = $this->enrollments
            ->where('Stud_ID', $studId)
            ->where('Enrollment_Status', 'ongoing')
            ->where('Enrollment_Date >=', $currentYear . '-01-01')
            ->findAll();

        // Get the course IDs from enrollments
        $courseIds = array_column($enrollments, 'Course_ID');

        // Find teaching assignments for these courses
        $teachingAssignments = $this->teacherAssignments
            ->whereIn('course_id', $courseIds)
            ->findAll();

        // Get the teacher IDs from teaching assignments
        $teacherIds = array_column($teachingAssignments, 'Teacher_ID');

        // Retrieve exams for these teachers
        $exams = $this->examModel->whereIn('Teacher_ID', $teacherIds)->findAll();

        // Add assignment status for each exam
        foreach ($exams as &$exam) {
            $assignment = $this->examAssignments
                ->where('Exam_ID', $exam['Exam_ID'])
                ->where('Stud_ID', $studId)
                ->first();

            $exam['Assignment_Status'] = $assignment ? $assignment['Assignment_Status'] : 'Not Taken';
        }

        return $this->respond($exams);
    }

    public function getStudentExam($id = null)
    {
        if (!$id) {
            return $this->failNotFound('Exam ID not provided');
        }

        $exam = $this->examModel->find((int) $id);
        if (!$exam) {
            return $this->failNotFound('Exam not found with ID: ' . $id);
        }

        // Fetch related questions
        $exam['questions'] = $this->questionsModel
            ->select('Question_ID, Exam_ID, Question_Text')
            ->where('Exam_ID', (int) $id)
            ->findAll();


        foreach ($exam['questions'] as $key => $question) {
            // Fetch choices for each question
            $choices = $this->choices->where('Question_ID', (int) $question['Question_ID'])->findAll();

            $exam['questions'][$key]['options'] = array_column($choices, 'Choice_Text');
        }

        return $this->respond($exam);
    }
    public function submitExamAnswers()
    {
        helper(['form', 'url']);

        $json = $this->request->getJSON();
        $examId = $json->examId;
        $userId = $json->userId;
        $answers = $json->answers;

        // Find or create the assignment entry
        $assignment = $this->examAssignments
            ->where('Exam_ID', $examId)
            ->where('Stud_ID', $userId)
            ->first();

        $student = $this->students->where('User_ID', (int) $userId)->first();

        if (!$assignment) {
            $assignmentId = $this->examAssignments->insert([
                'Stud_ID' => $student['Stud_ID'],
                'Exam_ID' => $examId,
                'Assignment_Status' => 'In Progress'
            ]);
        } else {
            $assignmentId = $assignment['Assignment_ID'];
        }

        // Save each answer and calculate score
        $correctCount = 0;
        foreach ($answers as $answer) {
            $correctAnswer = $this->questionsModel
                ->where('Question_ID', $answer->questionId)
                ->first()['Correct_Answer'];

            $response = [
                'Assignment_ID' => $assignmentId,
                'Question_ID' => $answer->questionId,
                'Selected_Answer' => $answer->selectedAnswer
            ];
            $this->responsesModel->insert($response);

            if ($answer->selectedAnswer === $correctAnswer) {
                $correctCount++; // Increment score for each correct answer
            }
        }

        // Calculate the score as a percentage
        $totalQuestions = count($answers);
        // $scorePercentage = ($totalQuestions > 0) ? ($correctCount / $totalQuestions) * 100 : 0;

        // Update the assignment status and score
        $this->examAssignments->update($assignmentId, [
            'Assignment_Status' => 'Completed',
            'Completion_Time' => date('Y-m-d H:i:s'),
            'Score' => $correctCount . '/' . $totalQuestions // Store the score as a rounded percentage
        ]);

        return $this->respondCreated(['success' => true, 'message' => 'Exam answers submitted successfully.']);
    }




}

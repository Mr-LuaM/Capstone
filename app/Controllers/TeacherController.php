<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\Debug\Toolbar\Collectors\Model;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\Files\File;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Validation\Exceptions\ValidationException;
use CodeIgniter\Validation\Validation;

class TeacherController extends BaseController
{
    public function getTeacherEditDetails()
    {
        // Get parameters from the request
        $role = $this->request->getVar('userRole');
        $secureToken = $this->request->getVar('secureToken');
        $userId = $this->request->getVar('userId');


        if (!(int) $role === 3) {
            return $this->failNotFound('Invalid role');
        }

        // Fetch all user details based on the user ID
        $userDetails = $this->teachers->where('User_ID', $userId)->first();

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

    public function updateTeacherDetails()
    {

        // Retrieve data from the request
        $userId = $this->request->getPost('userId');
        $userRole = $this->request->getPost('userRole');
        $secureToken = $this->request->getPost('secureToken');



        // Validate user role and secure token (add your own validation logic)
        if ($userRole !== '4') {
            return $this->failUnauthorized('Invalid role or secure token');
        }

        // Fetch the station record based on user ID
        $stationAdmin = $this->teachers->where('Teacher_ID', (int) $userId)->first();
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
            $this->teachers->update($userId, [
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
                'Teacher_PhoneNum' => $this->request->getPost('phoneNumber'),
            ]);
        } else {
            // Update stationAdmin details excluding the Profile_Picture field
            $this->teachers->update($userId, [
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
                'Teacher_PhoneNum' => $this->request->getPost('phoneNumber'),
                // Add other fields as needed
            ]);

        }

        return $this->respond(['success' => true, 'message' => 'User details updated']);

    }

    public function getStudents()
    {
        $loggedInUserID = $this->request->getVar('user_id');
        $teacherID = $this->db->table('teachers')
            ->select('Teacher_ID')
            ->where('User_ID', (int) $loggedInUserID)
            ->get()
            ->getRow()
            ->Teacher_ID;

        // Get teaching assignments for the teacher
        $teachingAssignments = $this->db->table('teachingassignments ta')
            ->select('ta.*, c.Course_Name, c.Duration, s.Station_Name, t.*')
            ->join('courses c', 'ta.course_id = c.Course_ID')
            ->join('stations s', 'ta.station_id = s.Station_ID')
            ->join('teachers t', 'ta.Teacher_ID = t.Teacher_ID')
            ->where('ta.Teacher_ID', $teacherID)
            ->get()
            ->getResult();

        // Array to store the result
        $result = [];

        // Iterate through teaching assignments
        foreach ($teachingAssignments as $teachingAssignment) {
            // Get the course ID and duration for each teaching assignment
            $courseID = $teachingAssignment->course_id;
            $duration = $teachingAssignment->Duration;

            // Get students enrolled in the course with status "ongoing" including the duration
            $students = $this->db->table('enrollments e')
                ->select('e.*, s.*, g.Grade, g.Assessment_Type, g.Comments, ' . $duration . ' as Duration') // Include the duration in the students' result
                ->join('students s', 'e.Stud_ID = s.Stud_ID')
                ->join('grades g', 'e.Enrollment_ID = g.Enrollment_ID', 'left') // Assuming a left join with grades table
                ->where('e.Course_ID', $courseID)
                // ->where('e.Enrollment_Status', 'ongoing')
                ->get()
                ->getResult();

            // Append the result for each course
            $result[] = [
                'teaching_assignment' => $teachingAssignment,
                'students' => $students,
            ];
        }

        // Respond with the result (you can format the response as needed)
        return $this->response->setJSON($result);
    }



    public function importGrades()
    {
        // Retrieve the uploaded file from the request
        $file = $this->request->getFile('grades_file');

        // Check if a file was uploaded
        if (!$file || $file->getError()) {
            // Respond with an error if no file was selected or if there's an error
            return $this->respond(['success' => false, 'message' => 'No file selected or file error']);
        }

        // Load the spreadsheet from the uploaded file
        $spreadsheet = IOFactory::load($file->getTempName());

        // Get the sheet by name (replace "Grades" with the actual sheet name)
        $sheet = $spreadsheet->getSheetByName("Grades");

        foreach ($sheet->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Assuming the columns are in a specific order
            $enrollmentID = $rowData[0] ?? null; // Assuming Enrollment_ID is in the first column
            $grade = $rowData[21] ?? null; // Assuming Grade is in the 21st column (adjust the index accordingly)

            // Check if both enrollmentID and grade are present
            if ($enrollmentID !== null && $grade !== null) {
                // Insert or update the grade in the database
                // Perform necessary validation and error handling

                // Example: Update existing grade if it exists, otherwise insert a new grade
                $existingGrade = $this->db->table('grades')->where('Enrollment_ID', $enrollmentID)->get()->getRow();

                if ($existingGrade) {
                    // Update the existing grade
                    $this->db->table('grades')
                        ->where('Enrollment_ID', $enrollmentID)
                        ->update(['Grade' => $grade]);
                } else {
                    // Insert a new grade
                    $this->db->table('grades')
                        ->insert(['Enrollment_ID' => $enrollmentID, 'Grade' => $grade]);
                }
            }
        }

        // Respond with success after processing all rows
        return $this->respond(['success' => true]);
    }

    public function saveGrade()
    {
        $Stud_ID = $this->request->getPost('Stud_ID');
        $gradeValue = $this->request->getPost('Grade');

        // Check if the enrollment exists
        $enrollment = $this->enrollments->where('Stud_ID', $Stud_ID)->first();

        if (!$enrollment) {
            return $this->fail('Enrollment not found', 404);
        }

        // Check if the grade is valid (numeric value)
        if (!is_numeric($gradeValue)) {
            return $this->fail('Invalid grade value', 400);
        }

        // Determine if the student passed or failed based on the grade
        $gradeStatus = ($gradeValue > 75) ? 'passed' : 'failed';

        // Check if a grade record already exists for the given Enrollment_ID
        $existingGrade = $this->grades->where('Enrollment_ID', $enrollment['Enrollment_ID'])->first();

        if ($existingGrade) {
            // Update the existing grade record
            // Update the existing grade record
            $this->grades->update($existingGrade['Grade_ID'], ['Grade' => $gradeValue]);

        } else {
            // Save the grade details as a new record
            $gradeData = [
                'Enrollment_ID' => $enrollment['Enrollment_ID'],
                'Grade' => $gradeValue,
                'Assessment_Type' => $this->request->getPost('Assessment_Type'),
                'Comments' => $this->request->getPost('Comments'),
            ];

            $this->grades->insert($gradeData);
        }

        // Update the enrollment status in the database
        $this->enrollments->update($enrollment['Enrollment_ID'], ['Enrollment_Status' => $gradeStatus]);

        return $this->respond(['success' => true, 'message' => 'Grade saved successfully']);
    }

    public function getExamsWithData()
    {
        try {
            // Fetch exams along with options and responses
            $exams = $this->examAssignments->getExamsWithData();

            // Return the data as JSON
            return $this->response->setJSON(['exams' => $exams]);
        } catch (\Exception $e) {
            // Handle errors
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function getTeacherSchedule()
    {
        try {
            $request = $this->request->getJSON(); // Assuming the data is sent as JSON

            // Get the user ID from the request
            $userId = (int) $request->teacherId;

            // Fetch the teacher ID from the teachers table based on the user ID
            $teacher = $this->teachers->where('User_ID', $userId)->first();

            if (!$teacher) {
                // Respond with an error if the teacher is not found
                return $this->failNotFound('Teacher not found for the given user ID.');
            }

            $teacherId = $teacher['Teacher_ID'];

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


    public function addExam()
    {
        helper(['form', 'url']);

        $examModel = $this->examModel;
        $questionModel = $this->questionsModel;
        $choicesModel = $this->choices;

        // Retrieve each part of the form data
        $examTitle = $this->request->getPost('examTitle');
        $questions = json_decode($this->request->getPost('questions'), true);
        $duration = $this->request->getPost('duration');
        $startDate = $this->request->getPost('startDate');
        $endDate = $this->request->getPost('endDate');
        $userID = $this->request->getPost('teacherId');
        $examId = $this->request->getPost('examId');
        $isUpdate = !empty($examId);

        $teacherId = $this->teachers->where('User_ID', (int) $userID)->first();

        // Insert exam data
        $examData = [
            'Teacher_ID' => $teacherId['Teacher_ID'],
            'Exam_Title' => $examTitle,
            'Duration_Minutes' => $duration,
            'Start_Time' => $startDate,
            'End_Time' => $endDate
        ];
        if ($isUpdate) {
            // Update the exam data
            $examModel->update($examId, $examData);

            // Fetch existing question IDs for this exam
            $existingQuestionIds = $questionModel->where('Exam_ID', $examId)->findColumn('Question_ID') ?? [];

            // IDs of questions that have been processed (either updated or newly created)
            $processedQuestionIds = [];

            foreach ($questions as $question) {
                $questionData = [
                    'Exam_ID' => $examId,
                    'Question_Text' => $question['text'],
                    'Correct_Answer' => $question['correctAnswer']
                ];

                $questionId = $question['Question_ID'] ?? null;
                if (!empty($questionId)) {
                    // Update existing question
                    $questionModel->update($questionId, $questionData);
                    $processedQuestionIds[] = $questionId;
                } else {
                    // Insert new question
                    if ($questionModel->insert($questionData) === false) {
                        return $this->failValidationErrors($questionModel->errors());
                    }
                    $newQuestionId = $questionModel->getInsertID();
                    $processedQuestionIds[] = $newQuestionId;
                    $questionId = $newQuestionId;
                }

                // Update choices
                // Delete existing choices for the question and insert new ones
                $choicesModel->where('Question_ID', $questionId)->delete();
                foreach ($question['options'] as $optionText) {
                    $choiceData = ['Question_ID' => $questionId, 'Choice_Text' => $optionText];
                    $choicesModel->insert($choiceData);
                }
            }

            // Determine which questions to delete (existing in DB but not in the processed list)
            $questionsToDelete = array_diff($existingQuestionIds, $processedQuestionIds);

            // Delete unprocessed questions and their choices
            foreach ($questionsToDelete as $questionId) {
                $choicesModel->where('Question_ID', $questionId)->delete();
                $questionModel->delete($questionId);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Exam updated successfully']);
        } else {

            if ($examModel->insert($examData) === false) {
                return $this->failValidationErrors($examModel->errors());
            }

            $examId = $examModel->getInsertID();

            // Process each question
            foreach ($questions as $question) {
                $questionData = [
                    'Exam_ID' => $examId,
                    'Question_Text' => $question['text'],
                    'Correct_Answer' => $question['correctAnswer']
                ];

                if ($questionModel->insert($questionData) === false) {
                    return $this->failValidationErrors($questionModel->errors());
                }

                $questionId = $questionModel->getInsertID();

                // Process each option for the question
                foreach ($question['options'] as $option) {
                    $choiceData = [
                        'Question_ID' => $questionId,
                        'Choice_Text' => $option
                    ];
                    $choicesModel->insert($choiceData);
                }
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Exam added successfully']);

        }


    }
    public function getExams()
    {
        $userId = $this->request->getVar('userId');


        $teacher = $this->teachers->where('User_ID', (int) $userId)->first();

        if (!$teacher) {
            return $this->failNotFound('Teacher not found.');
        }

        $teacherId = $teacher['Teacher_ID'];

        // Fetch exams for this teacher
        $exams = $this->examModel->where('Teacher_ID', $teacherId)->findAll();

        return $this->respond($exams);
    }

    public function getExam($id = null)
    {
        if (!$id) {
            return $this->failNotFound('Exam ID not provided');
        }

        $exam = $this->examModel->find((int) $id);
        if (!$exam) {
            return $this->failNotFound('Exam not found with ID: ' . $id);
        }

        // Fetch related questions
        $exam['questions'] = $this->questionsModel->where('Exam_ID', (int) $id)->findAll();

        foreach ($exam['questions'] as $key => $question) {
            // Fetch choices for each question
            $choices = $this->choices->where('Question_ID', (int) $question['Question_ID'])->findAll();

            $exam['questions'][$key]['options'] = array_column($choices, 'Choice_Text');
        }

        return $this->respond($exam);
    }
    public function getResponse($examId)
    {
        helper(['form', 'url']);

        if (!$examId) {
            return $this->failNotFound('Exam ID not provided');
        }


        // Ensure $examId is a valid number
        if (!is_numeric($examId)) {
            return $this->failValidationErrors('Invalid exam ID');
        }

        $builder = $this->db->table('examassignments');
        $builder->join('students', 'students.Stud_ID = examassignments.Stud_ID');
        $builder->where('examassignments.Exam_ID', (int) $examId);
        $builder->select('examassignments.*, students.*, students.first_name as studentFirstName, students.last_name as studentLastName');

        $query = $builder->get();

        if (!$query) {
            return $this->failNotFound('No responses found for this exam.');
        }

        return $this->respond($query->getResultArray());
    }












}

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

class TeacherController extends BaseController {
    public function getTeacherEditDetails() {
        // Get parameters from the request
        $role = $this->request->getVar('userRole');
        $secureToken = $this->request->getVar('secureToken');
        $userId = $this->request->getVar('userId');


        if(!(int)$role === 3) {
            return $this->failNotFound('Invalid role');
        }

        // Fetch all user details based on the user ID
        $userDetails = $this->teachers->where('User_ID', $userId)->first();

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

    public function updateTeacherDetails() {

        // Retrieve data from the request
        $userId = $this->request->getPost('userId');
        $userRole = $this->request->getPost('userRole');
        $secureToken = $this->request->getPost('secureToken');



        // Validate user role and secure token (add your own validation logic)
        if($userRole !== '4') {
            return $this->failUnauthorized('Invalid role or secure token');
        }

        // Fetch the station record based on user ID
        $stationAdmin = $this->teachers->where('Teacher_ID', (int)$userId)->first();
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
            $this->teachers->update($userId, [
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

    public function getStudents() {
        $loggedInUserID = $this->request->getVar('user_id');
        $teacherID = $this->db->table('teachers')
            ->select('Teacher_ID')
            ->where('User_ID', (int)$loggedInUserID)
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
        foreach($teachingAssignments as $teachingAssignment) {
            // Get the course ID and duration for each teaching assignment
            $courseID = $teachingAssignment->course_id;
            $duration = $teachingAssignment->Duration;

            // Get students enrolled in the course with status "ongoing" including the duration
            $students = $this->db->table('enrollments e')
                ->select('e.*, s.*, g.Grade, g.Assessment_Type, g.Comments, '.$duration.' as Duration') // Include the duration in the students' result
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



    public function importGrades() {

        // Retrieve the uploaded file from the request
        $file = $this->request->getFile('grades_file');



        // Check if a file was uploaded
        if($file) {

            // Load the spreadsheet from the uploaded file
            $spreadsheet = IOFactory::load($file->getTempName());

            // Get the sheet by name (replace "Grades" with the actual sheet name)
            $sheet = $spreadsheet->getSheetByName("Grades");
            return $this->respond($sheet);

            foreach($sheet->getRowIterator() as $row) {
                $rowData = [];
                foreach($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }

                // Assuming the columns are in a specific order
                $enrollmentID = $rowData[0] ?? null; // Assuming Enrollment_ID is in the first column
                $grade = $rowData[20] ?? null; // Assuming Grade is in the 21st column (adjust the index accordingly)

                // Check if both enrollmentID and grade are present
                if($enrollmentID !== null && $grade !== null) {
                    // Insert or update the grade in the database
                    // Perform necessary validation and error handling

                    // Example: Update existing grade if it exists, otherwise insert a new grade
                    $existingGrade = $this->db->table('grades')->where('Enrollment_ID', $enrollmentID)->get()->getRow();

                    if($existingGrade) {
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

            // Respond with success or any additional logic as needed
            return $this->respond(['success' => true]);
        } else {
            // Respond with an error if no file was selected
            return $this->respond(['success' => false, 'message' => 'No file selected']);
        }
    }
    public function saveGrade() {
        $Stud_ID = $this->request->getPost('Stud_ID');
        $gradeValue = $this->request->getPost('Grade');

        // Check if the enrollment exists
        $enrollment = $this->enrollments->where('Stud_ID', $Stud_ID)->first();

        if(!$enrollment) {
            return $this->fail('Enrollment not found', 404);
        }

        // Check if the grade is valid (numeric value)
        if(!is_numeric($gradeValue)) {
            return $this->fail('Invalid grade value', 400);
        }

        // Determine if the student passed or failed based on the grade
        $gradeStatus = ($gradeValue > 75) ? 'passed' : 'failed';

        // Check if a grade record already exists for the given Enrollment_ID
        $existingGrade = $this->grades->where('Enrollment_ID', $enrollment['Enrollment_ID'])->first();

        if($existingGrade) {
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








}

<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->get('/getEnrollmentDetails', 'DashboardController::getEnrollmentDetails');
$routes->get('/getUserStatistics', 'DashboardController::getUserStatistics');
$routes->get('/getStationAdminStatistics', 'DashboardController::getStationAdminStatistics');
$routes->get('/getTeacherStatistics', 'DashboardController::getTeacherStatistics');
$routes->get('/getStudentStatistics', 'DashboardController::getStudentStatistics');
$routes->get('/getApplicantsStatistics', 'DashboardController::getApplicantsStatistics');
$routes->get('/getEnrollmentTrends', 'DashboardController::getEnrollmentTrends');
$routes->get('/getCourseTrends', 'DashboardController::getCourseTrends');
$routes->get('/getStudentDemographics', 'DashboardController::getStudentDemographics');
$routes->get('/getStudentStatisticsperStation/(:num)', 'StationAdminController::getStudentStatisticsperStation/$1');
$routes->get('/getTeacherStatisticsperStation/(:num)', 'StationAdminController::getTeacherStatisticsperStation/$1');



$routes->post('/checkEmail', 'AuthController::checkEmail');
$routes->post('/login', 'AuthController::login');
$routes->post('auth/verify/(:any)', 'AuthController::verifyAccount/$1');
$routes->get('getUserDetails/(:segment)/(:num)', 'AuthController::getUserDetails/$1/$2');
$routes->post('/createAccount', 'AuthController::createAccount');
$routes->post('/save-token', 'AuthController::saveToken');

$routes->post('updateUserDetails', 'MainController::updateUserDetails');




$routes->get('/getCourses', 'MainController::getCourses');
$routes->get('/getStations', 'MainController::getStations');
$routes->get('/getStation', 'MainController::getStation');
$routes->get('/getCourse', 'MainController::getCourse');
$routes->post('/getRoles', 'MainController::getRoles');
$routes->get('/getCoursesByStation/(:num)', 'MainController::getCoursesByStation/$1');
$routes->get('getAnnouncements/(:segment)/(:segment)', 'MainController::getAnnouncements/$1/$2');
$routes->get('getCoursesInStation/(:segment)', 'MainController::getCoursesInStation/$1');

$routes->post('/submitApplication', 'ApplicantsController::submitApplication');


$routes->get('/getApplicants', 'MainAdminController::getApplicants');
$routes->get('/getApplicantsHistory', 'MainAdminController::getApplicantsHistory');
$routes->post('approve', 'MainAdminController::approve');
$routes->post('reject/(:any)', 'MainAdminController::reject/$1');
$routes->post('editStation', 'MainAdminController::editStation');
$routes->post('addStation', 'MainAdminController::addStation');
$routes->post('toggleStatus/(:num)', 'MainAdminController::toggleStatus/$1');
$routes->post('addCourse', 'MainAdminController::addCourse');
$routes->post('editCourse', 'MainAdminController::editCourse');
$routes->post('toggleCourseStatus/(:num)', 'MainAdminController::toggleCourseStatus/$1');
$routes->get('/getStationAdminsWithStation', 'MainAdminController::getStationAdminsWithStation');
$routes->post('toggleStationAdminStatus/(:num)', 'MainAdminController::toggleStationAdminStatus/$1');
$routes->post('changeAdminStation', 'MainAdminController::changeAdminStation');
$routes->post('getAdminEditDetails', 'MainAdminController::getAdminEditDetails');
$routes->post('updateMainAdminDetails', 'MainAdminController::updateMainAdminDetails');
$routes->post('updateAnnouncement', 'MainAdminController::updateAnnouncement');
$routes->post('addAnnouncement', 'MainAdminController::addAnnouncement');
$routes->post('removeAnnouncement/(:num)', 'MainAdminController::removeAnnouncement/$1');
$routes->get('/getTeacherAssignmentsDetails', 'MainAdminController::getTeacherAssignmentsDetails');
$routes->post('toggleTeacherStatus/(:num)', 'MainAdminController::toggleTeacherStatus/$1');
$routes->post('changeTeacherStation', 'MainAdminController::changeTeacherStation');
$routes->post('changeTeacherCourse', 'MainAdminController::changeTeacherCourse');


$routes->post('updateStationAdminDetails', 'StationAdminController::updateStationAdminDetails');
$routes->post('getStationAdminEditDetails', 'StationAdminController::getStationAdminEditDetails');
$routes->post('getStationDetailsperStation', 'StationAdminController::getStationDetailsperStation');
$routes->post('saveStationChanges', 'StationAdminController::saveStationChanges');
$routes->post('/getTeacherAssignmentsDetailsperStation', 'StationAdminController::getTeacherAssignmentsDetailsperStation');
$routes->post('/getEnrollmentsByCourse', 'StationAdminController::getEnrollmentsByCourse');
$routes->post('/saveSchedule', 'StationAdminController::saveSchedule');
$routes->post('/getTeacherSchedule', 'StationAdminController::getTeacherSchedule');



$routes->post('updateTeacherDetails', 'TeacherController::updateTeacherDetails');
$routes->post('getTeacherEditDetails', 'TeacherController::getTeacherEditDetails');
$routes->post('getStudents', 'TeacherController::getStudents');
$routes->post('importGradesHandler', 'TeacherController::importGrades');
$routes->post('editGrade', 'TeacherController::saveGrade');
$routes->get('fetch-exams-with-options-and-responses', 'TeacherController::fetch-exams-with-options-and-responses');
$routes->post('/getTeacherSchedule1', 'TeacherController::getTeacherSchedule');
$routes->post('/addExam', 'TeacherController::addExam');
$routes->post('/getExams', 'TeacherController::getExams');
$routes->get('/getExam/(:segment)', 'TeacherController::getExam/$1');
$routes->get('/getResponse/(:segment)', 'TeacherController::getResponse/$1');




$routes->post('getstudentEditDetails', 'StudentController::getstudentEditDetails');
$routes->post('updateStudentDetails', 'StudentController::updateStudentDetails');
$routes->post('getStudentSchedule', 'StudentController::getStudentSchedule');
$routes->post('studentData', 'StudentController::getStudentData');
$routes->post('/getStudentProgressByUserId', 'StudentController::getStudentProgressByUserId');
$routes->post('/getStudentExams', 'StudentController::getStudentExams');
$routes->get('/getStudentExam/(:segment)', 'StudentController::getStudentExam/$1');
$routes->post('/submitExamAnswers', 'StudentController::submitExamAnswers');





$routes->get('generateSecureToken/(:num)', 'AuthController::generateSecureToken/$1');

// $routes->get('/test', 'MainController::test');
// $routes->get('/testmail', 'ApplicantsController::testEmail');






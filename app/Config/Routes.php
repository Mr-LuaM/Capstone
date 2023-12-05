<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->get('/getEnrollmentDetails', 'DashboardController::getEnrollmentDetails');



$routes->post('/checkEmail', 'AuthController::checkEmail');
$routes->post('/login', 'AuthController::login');
$routes->post('auth/verify/(:any)', 'AuthController::verifyAccount/$1');
$routes->get('getUserDetails/(:segment)/(:num)', 'AuthController::getUserDetails/$1/$2');
$routes->post('/createAccount', 'AuthController::createAccount');

$routes->post('updateUserDetails', 'MainController::updateUserDetails');




$routes->get('/getCourses', 'MainController::getCourses');
$routes->get('/getStations', 'MainController::getStations');
$routes->get('/getStation', 'MainController::getStation');
$routes->get('/getCourse', 'MainController::getCourse');
$routes->post('/getRoles', 'MainController::getRoles');
$routes->get('/getCoursesByStation/(:num)', 'MainController::getCoursesByStation/$1');
$routes->get('/getAnnouncements', 'MainController::getAnnouncements');


$routes->post('/submitApplication', 'ApplicantsController::submitApplication');


$routes->get('/getApplicants', 'MainAdminController::getApplicants');
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





$routes->get('generateSecureToken/(:num)', 'AuthController::generateSecureToken/$1');

// $routes->get('/test', 'MainController::test');
// $routes->get('/testmail', 'ApplicantsController::testEmail');






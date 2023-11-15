<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/getCourses', 'MainController::getCourses');
$routes->get('/getStations', 'MainController::getStations');
$routes->get('/getStation', 'MainController::getStation');


$routes->post('/submitApplication', 'ApplicantsController::submitApplication');
$routes->get('/getApplicants', 'AdminController::getApplicants');
$routes->post('/approve', 'ApplicantsController::approve');


$routes->get('/test', 'ApplicantsController::test');

$routes->get('generateSecureToken/(:num)', 'AuthController::generateSecureToken/$1');
$routes->post('reject/(:any)', 'ApplicantsController::reject/$1');


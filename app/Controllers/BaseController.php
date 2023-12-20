<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\Request;
use CodeIgniter\API\ResponseTrait;


/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends ResourceController
{
    use ResponseTrait;

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;
    protected $courses;
    protected $stationCourses;
    protected $stations;

    protected $applicants;
    protected $users;
    protected $students;
    protected $roles;
    protected $enrollments;
    protected $db;
    protected $validation;
    protected $StationAdmin;
    protected $mainAdmin;
    protected $annoucements;
    protected $teachers;
    protected $teacherAssignments;
    protected $examAssignments;
    protected $examModel;
    protected $questionsModel;
    protected $responsesModel;
    protected $grades;
    protected $dailyschedule;

    protected $StationAnnouncements;
    protected $choices;
    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->validation = \Config\Services::validation();
        $this->courses = new \App\Models\CoursesModel;
        $this->stationCourses = new \App\Models\StationCoursesModel;
        $this->stations = new \App\Models\StationsModel;
        $this->applicants = new \App\Models\ApplicantsModel;
        $this->students = new \App\Models\StudentsModel;
        $this->users = new \App\Models\UserModel;
        $this->enrollments = new \App\Models\EnrollmentsModel;
        $this->StationAdmin = new \App\Models\StationAdminModel;
        $this->mainAdmin = new \App\Models\MainAdminModel();
        $this->roles = new \App\Models\RoleModel();
        $this->annoucements = new \App\Models\AnnouncementModel();
        $this->teachers = new \App\Models\TeachersModel();
        $this->teacherAssignments = new \App\Models\TeacherAssignments();
        $this->grades = new \App\Models\GradeModel();
        $this->examAssignments = new \App\Models\ExamAssignmentsModel();
        $this->examModel = new \App\Models\ExamModel();
        $this->choices = new \App\Models\ChoicesModel;
        $this->questionsModel = new \App\Models\QuestionsModel();
        $this->responsesModel = new \App\Models\ResponsessModel();
        $this->dailyschedule = new \App\Models\DailyScheduleModel();
        $this->StationAnnouncements = new \App\Models\StationAnnouncementModel;


    }
}

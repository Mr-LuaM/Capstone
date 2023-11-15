<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;


class AdminController extends ResourceController
{
    use ResponseTrait;
    private $applicants;

    public function __construct()
    {
        $this->applicants = new \App\Models\ApplicantsModel;
    }
    public function getApplicants()
    {
        $data = $this->applicants->findAll();
        if (empty($data)) {
            return $this->failNotFound('No Courses Found');
        }
        return $this->respond($data);

    }
}

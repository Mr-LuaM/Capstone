<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantsModel extends Model {
    protected $table = 'applicants';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['Stud_ID', 'Last_Name', 'First_Name', 'Middle_Name', 'Name_Extension', 'Sex', 'Bdate', 'Age', 'Nationality', 'Religion', 'Height', 'Weight', 'Address', 'Email', 'Phone_Number', 'Selected_File1', 'Course1', 'Station1', 'Course2', 'Station2', 'Course3', 'Station3', 'Date_Of_Application', 'Status', 'User_ID'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];
}

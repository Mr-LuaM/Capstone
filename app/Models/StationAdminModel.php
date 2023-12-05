<?php

namespace App\Models;

use CodeIgniter\Model;

class StationAdminModel extends Model {
    protected $table = 'stationadmins';
    protected $primaryKey = 'StationAdmin_ID';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['User_ID', 'Station_ID', 'Profile_Picture', 'First_Name', 'Middle_Name', 'Last_Name', 'Name_Extension', 'Age', 'Sex', 'Address', 'Birthday', 'Birthplace', 'Status', 'Nationality', 'Religion', 'Admin_PhoneNum'];

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

<?php

namespace App\Services;
use App\Entities\Company;

class RemindersService extends BaseService
{

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM reminder ORDER BY created ASC");
    }

}

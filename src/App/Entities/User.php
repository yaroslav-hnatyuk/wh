<?php

namespace App\Entities;

class User extends BaseEntity
{
    public $id;
    public $email;
    public $role;
    public $first_name;
    public $last_name;
    public $phone;
    public $office_id;

    public function __construct($data)
    {
        $this->setProperties($data);
    }


}

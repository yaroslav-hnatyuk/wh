<?php

namespace App\Entities;

class User extends BaseEntity
{
    public $id;
    public $email;
    public $role;
    public $first_name;
    public $last_name;
    public $ipn;
    public $pass;
    public $salt;
    public $phone;
    public $office_id;
    public $is_feedback_active;
    public $is_active;

    public function __construct($data)
    {
        $this->setProperties($data);
    }
    
}

<?php

namespace App\Entities;

class Office extends BaseEntity
{
    public $id;
    public $address;
    public $company_id;
    public $uid;

    public function __construct($data)
    {
        $this->setProperties($data);
    }

}

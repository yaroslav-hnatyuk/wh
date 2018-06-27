<?php

namespace App\Entities;

class Company extends BaseEntity
{
    public $id;
    public $name;

    public function __construct($data)
    {
        $this->setProperties($data);
    }

}

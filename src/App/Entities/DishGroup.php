<?php

namespace App\Entities;

class DishGroup extends BaseEntity
{
    public $id;
    public $name;

    public function __construct($data)
    {
        $this->setProperties($data);
    }

}

<?php

namespace App\Entities;

class MenuDish extends BaseEntity
{
    public $id;
    public $start;
    public $end;
    public $dish_id;

    public function __construct($data)
    {
        $this->setProperties($data);
    }


}

<?php

namespace App\Entities;

class Rating extends BaseEntity
{
    public $id;
    public $mark;
    public $user_id;
    public $dish_id;

    public function __construct($data)
    {
        $this->setProperties($data);
    }


}

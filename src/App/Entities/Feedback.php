<?php

namespace App\Entities;

class Feedback extends BaseEntity
{
    public $id;
    public $text;
    public $created;
    public $user_id;
    public $dish_id;

    public function __construct($data)
    {
        $this->setProperties($data);
    }


}

<?php

namespace App\Entities;

class Dish extends BaseEntity
{
    public $id;
    public $name;
    public $price;
    public $weight;
    public $calories;
    public $description;
    public $ingredients;
    public $dish_group_id;

    public function __construct($data)
    {
        $this->setProperties($data);
    }


}

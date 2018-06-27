<?php

namespace App\Entities;

class Order extends BaseEntity
{
    public $id;
    public $day;
    public $count;
    public $user_id;
    public $menu_dish_id;

    public function __construct($data)
    {
        $this->setProperties($data);
    }


}

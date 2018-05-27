<?php

namespace App\Services;
use App\Entities\Dish;

class DishesService extends BaseService
{

    public function getOne($id)
    {
        $data = $this->db->fetchAssoc("SELECT * FROM dish WHERE id=?", [(int) $id]);
        $dish = new Dish($data);

        return $dish->getArray();
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM dish");
    }

    function save($data = array())
    {
        $dish = new Dish($data);
        $this->db->insert("dish", $dish->getArray());
        $dish->id = $this->db->lastInsertId();

        return $dish->getArray();
    }

    function update($data = array())
    {
        $dish = new Dish($data);
        $this->db->update('dish', $dish->getArray(), ['id' => $dish->id]);

        return $dish->getArray();
    }

    function delete($id)
    {
        return $this->db->delete("dish", array("id" => $id));
    }

}

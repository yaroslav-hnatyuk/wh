<?php

namespace App\Services;
use App\Entities\MenuDish;

class MenudishesService extends BaseService
{

    public function getOne($id)
    {
        $data = $this->db->fetchAssoc("SELECT * FROM menu_dish WHERE id=?", [(int) $id]);
        $menuDish = new MenuDish($data);

        return $menuDish->getArray();
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM menu_dish");
    }

    function save($data = array())
    {
        $menuDish = new MenuDish($data);
        $this->db->insert("menu_dish", $menuDish->getArray());
        $menuDish->id = $this->db->lastInsertId();

        return $menuDish->getArray();
    }

    function update($data = array())
    {
        $menuDish = new MenuDish($data);
        $this->db->update('menu_dish', $menuDish->getArray(), ['id' => $menuDish->id]);

        return $menuDish->getArray();
    }

    function delete($id)
    {
        return $this->db->delete("menu_dish", array("id" => $id));
    }

}

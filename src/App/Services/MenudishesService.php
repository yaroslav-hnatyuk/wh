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

    public function getForPeriod($period, $group = false)
    {
        $result = $this->db->fetchAll("SELECT 
            d.id as dish_id,
            d.name as dish_name,
            d.description as description,
            d.ingredients as ingredients,
            d.weight as weight,
            d.price as price,
            g.id as group_id,
            g.name as group_name,
            m.id as menu_id,
            m.start as start,
            m.end as end
            FROM menu_dish m, dish d, dish_group g
            WHERE m.dish_id = d.id AND d.dish_group_id = g.id
            AND m.start >= '2018-05-28' AND m.end <='2018-06-03'"
        );

        return $group ? $this->groupMenuDishes($result) : $result;
    }

    public function groupMenuDishes($dishes)
    {
        $result = array();
        foreach ($dishes as $dish) {
            if (!isset($result[$dish['group_id']])) {
                $result[$dish['group_id']] = array(
                    'group_id' => $dish['group_id'],
                    'group_name' => $dish['group_name'],
                    'dishes' => array()
                );
            }
            $result[$dish['group_id']]['dishes'][] = $dish; 
        }

        return $result;
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

<?php

namespace App\Services;
use App\Entities\MenuDish;

class MenudishesService extends BaseService
{

    public function getOne($id)
    {
        $data = $this->db->fetchAssoc("SELECT * FROM menu_dish WHERE id=?", array((int) $id));
        $menuDish = new MenuDish($data);

        return $menuDish->getArray();
    }

    public function getOneByDishIdAndPeriod($dishId, $period) {
        return $this->db->fetchAssoc(
            "SELECT * FROM `menu_dish` WHERE `dish_id`=? AND `start`=? AND `end`=?", 
            array((int) $id, $period['start'], $period['end'])
        );
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM menu_dish");
    }

    public function getForPeriodForOrders($period)
    {
        return $this->db->fetchAll("SELECT 
            d.id as dish_id,
            d.name as dish_name,
            d.description as description,
            d.ingredients as ingredients,
            d.calories as calories,
            d.weight as weight,
            d.price as price,
            g.id as group_id,
            g.name as group_name,
            g.is_lunch as is_lunch,
            m.id as menu_id,
            m.start as start,
            m.end as end
            FROM menu_dish m, dish d, dish_group g
            WHERE m.dish_id = d.id AND d.dish_group_id = g.id
            AND m.start >= ? AND m.end <= ?", 
            array($period['start']['date'], $period['end']['date'])
        );
    }

    public function getForPeriod($period, $group = false)
    {
        $result = $this->db->fetchAll("SELECT 
            m.id as menu_id,
            m.dish_id as dish_id,
            m.start as start,
            m.end as end
            FROM menu_dish m
            WHERE m.start >= ? AND m.end <= ?", 
            array($period['start']['date'], $period['end']['date'])
        );

        return $group ? $this->groupByDishId($result) : $result;
    }

    public function groupByDishId($menu)
    {
        $result = array();
        foreach ($menu as $menuDish) {
            $result[$menuDish['dish_id']] = $menuDish;
        }

        return $result;
    }

    public function groupMenuDishes($dishes)
    {
        $result = array();
        foreach ($dishes as &$dish) {
            if (!isset($result[$dish['group_id']])) {
                $result[$dish['group_id']] = array(
                    'group_id' => $dish['group_id'],
                    'group_name' => $dish['group_name'],
                    'dishes' => array()
                );
            }
            if (isset($dish['orders'])) {
                $dish['orders_count'] = array_sum($dish['orders']);
            }
            $result[$dish['group_id']]['dishes'][] = $dish; 
        }

        unset($dish);
        ksort($result);

        return $result;
    }

    function save($data = array())
    {
        $menuDish = new MenuDish($data);
        $existingMenuDish = $this->getOneByDishIdAndPeriod(
            $menuDish->dish_id, 
            array(
                'start' => $menuDish->start, 
                'end' => $menuDish->end
            )
        );

        if ($existingMenuDish) {
            return $existingMenuDish;
        }

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

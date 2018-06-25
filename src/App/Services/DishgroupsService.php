<?php

namespace App\Services;
use App\Entities\DishGroup;

class DishgroupsService extends BaseService
{

    public function getOne($id)
    {
        $data = $this->db->fetchAssoc("SELECT * FROM dish_group WHERE id=?", [(int) $id]);
        $dishGroup = new DishGroup($data);

        return $dishGroup->getArray();
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM dish_group");
    }

    public function getMaxOrder() 
    {
        return $this->db->fetchColumn("SELECT MAX(`order`) FROM `dish_group`");
    }

    function save($data = array())
    {
        $this->db->prepare("INSERT INTO dish_group (`name`, `is_lunch`, `order`) VALUES (:name, :is_lunch, :order)")->execute($data);
        $data['id'] = $this->db->lastInsertId();

        return $data;
    }

    function update($data = array())
    {
        $dishGroup = new DishGroup($data);
        $this->db->update('dish_group', array('name' => $dishGroup->name), array('id' => $dishGroup->id));

        return $dishGroup->getArray();
    }

    function saveGroups($groups)
    {
        if (is_array($groups)) {
            foreach ($groups as $group) {
                $dishGroup = $this->getOne($group['id']);
                if ($dishGroup->id) {
                    $this->db->update('dish_group', array('name' => $group['name']), array('id' => $dishGroup->id));
                }
            }
        }
        die;
        return $groups;
    }

    function delete($id)
    {
        return $this->db->delete("dish_group", array("id" => $id));
    }

    function getAllAndGroupById()
    {
        $groups = $this->getAll();

        $result = array();
        foreach ($groups as $group) {
            $result[$group['id']] = $group;
            $result[$group['id']]['dishes'] = array();
        }

        return $result;
    }

    function groupDishes($dishes)
    {
        $groups = $this->getAllAndGroupById();
        foreach ($dishes as $dish) {
            if (isset($groups[$dish['dish_group_id']])) {
                $groups[$dish['dish_group_id']]['dishes'][] = $dish;
            }
        }

        return $groups;
    }

}

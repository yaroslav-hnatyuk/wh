<?php

namespace App\Services;
use App\Entities\DishGroup;

class DishgroupsService extends BaseService
{

    public function getOne($id)
    {
        $data =$this->db->fetchAssoc("SELECT * FROM dish_group WHERE id=?", [(int) $id]);
        $dishGroup = new DishGroup($data);

        return $dishGroup->getArray();
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM dish_group");
    }

    function save($data = array())
    {
        $dishGroup = new DishGroup($data);
        $this->db->insert("dish_group", $dishGroup->getArray());
        $dishGroup->id = $this->db->lastInsertId();

        return $dishGroup->getArray();
    }

    function update($data = array())
    {
        $dishGroup = new DishGroup($data);
        $this->db->update('dish_group', $dishGroup->getArray(), ['id' => $dishGroup->id]);

        return $dishGroup->getArray();
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

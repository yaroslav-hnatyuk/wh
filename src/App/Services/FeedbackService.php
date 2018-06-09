<?php

namespace App\Services;
use App\Entities\Feedback;

class FeedbackService extends BaseService
{

    public function getOne($id)
    {
        $data = $this->db->fetchAssoc("SELECT * FROM `feedback` WHERE id=?", [(int) $id]);
        $feedback = new Feedback($data);

        return $feedback->getArray();
    }

    public function getCountByDishId($dishId) 
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM `feedback` WHERE dish_id=?", [(int) $dishId]);
    }

    public function getAllByDishId($dishId) 
    {
        return $this->db->fetchAll("SELECT * FROM `feedback` WHERE dish_id=?", [(int) $dishId]);
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM `feedback`");
    }

    function save($data = array())
    {
        $feedback = new Feedback($data);
        $this->db->insert("`feedback`", $feedback->getArray());
        $feedback->id = $this->db->lastInsertId();

        return $feedback->getArray();
    }

    function update($data = array())
    {
        $feedback = new Feedback($data);
        $this->db->update('`feedback`', $feedback->getArray(), ['id' => $feedback->id]);

        return $feedback->getArray();
    }

    function delete($id)
    {
        return $this->db->delete("`feedback`", array("id" => $id));
    }

}

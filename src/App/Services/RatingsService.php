<?php

namespace App\Services;
use App\Entities\Rating;

class RatingsService extends BaseService
{

    public function getOne($id)
    {
        $data = $this->db->fetchAssoc("SELECT * FROM `rating` WHERE id=?", [(int) $id]);
        $rating = new Rating($data);

        return $rating->getArray();
    }

    public function getOneByDishIdAndUserId($dishId, $userId) 
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM `rating` WHERE dish_id=? AND user_id=?", 
            array($dishId, $userId)
        );
    }

    public function getAverageByDishId($dishId) 
    {
        $average = $this->db->fetchColumn("SELECT AVG(mark) FROM `rating` WHERE dish_id=?", [(int) $dishId]);
        return round($average);
    }

    public function getAllByDishId($dishId) 
    {
        return $this->db->fetchAll("SELECT * FROM `rating` WHERE dish_id=?", [(int) $dishId]);
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM `rating`");
    }

    function save($data = array())
    {
        $rating = $this->getOneByDishIdAndUserId($data['dish_id'], $data['user_id']);
        if ($rating) {
            $this->update($rating['id'], $data);
            $rating['id'] = $rating['id'];
        } else {
            $rating = new Rating($data);
            $this->db->insert("`rating`", $rating->getArray());
            $rating->id = $this->db->lastInsertId();    
        }

        return is_array($rating) ? $rating : $rating->getArray();
    }

    function update($id, $data = array())
    {
        return $this->db->update('`rating`', $data, ['id' => $id]);
    }

    function delete($id)
    {
        return $this->db->delete("`rating`", array("id" => $id));
    }

}

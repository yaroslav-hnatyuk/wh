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

    public function getGroupedByDishId($dishId) 
    {
        $result = array();
        
        $ratings = $this->db->fetchAll(
            "SELECT rating.*, CONCAT(user.first_name, ' ', user.last_name) as user_name FROM `rating`, `user` WHERE `rating`.user_id = `user`.id AND `rating`.dish_id=?", 
            [(int) $dishId]);
        
        foreach ($ratings as $rating) {
            if (!isset($result[$rating['mark']])) {
                $result[$rating['mark']] = 0;
            }
            $result[$rating['mark']]++;
        }
        
        return $result;
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

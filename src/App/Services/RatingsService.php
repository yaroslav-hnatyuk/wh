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

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM `rating`");
    }

    function save($data = array())
    {
        $rating = new Rating($data);
        $this->db->insert("`rating`", $rating->getArray());
        $rating->id = $this->db->lastInsertId();

        return $rating->getArray();
    }

    function update($data = array())
    {
        $rating = new Rating($data);
        $this->db->update('`rating`', $rating->getArray(), ['id' => $rating->id]);

        return $rating->getArray();
    }

    function delete($id)
    {
        return $this->db->delete("`rating`", array("id" => $id));
    }

}

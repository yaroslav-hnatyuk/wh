<?php

namespace App\Services;
use App\Entities\User;

class UsersService extends BaseService
{

    public function getOne($id)
    {
        $data =$this->db->fetchAssoc("SELECT * FROM user WHERE id=?", [(int) $id]);
        $user = new User($data);
        return $user->getArray();
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM user");
    }

    function save($data = array())
    {
        $user = new User($data);
        $this->db->insert("user", $user->getArray());
        return $this->db->lastInsertId();
    }

    function update($id, $user)
    {
        return $this->db->update('user', $user, ['id' => $id]);
    }

    function delete($id)
    {
        return $this->db->delete("user", array("id" => $id));
    }

}

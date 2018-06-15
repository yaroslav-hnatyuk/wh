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

    public function getByEmail($email) 
    {
        $data = $this->db->fetchAssoc("SELECT * FROM user WHERE email=?", [$email]);
        $user = new User($data);

        return $user;
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM user");
    }

    public function getAllWithCompaniesAndOffices()
    {
        return $this->db->fetchAll("SELECT
            user.id as user_id,
            CONCAT(user.first_name, \" \", user.last_name) as user_name,
            user.email as email,
            user.role as role,
            user.phone as phone,
            company.id as company_id,
            company.name as company_name,
            office.id as office_id,
            office.address as office_addr
            FROM user
            LEFT JOIN office ON user.office_id = office.id
            LEFT JOIN company ON office.company_id = company.id"
        );
    }

    public function getUsersByFilters($filters)
    {
        $sql = "SELECT u.* FROM `user` u, `office` o WHERE u.office_id = o.id";
        
        if (empty($filters['company']) && empty($filters['office']) && empty($filters['user'])) {
            return array();
        }

        if (!empty($filters['company'])) {
            $sql .= " AND o.company_id = ?";
            $params[] = $filters['company'];
        }

        if (!empty($filters['office'])) {
            $sql .= " AND u.office_id = ?";
            $params[] = $filters['office'];
        }

        if (!empty($filters['user'])) {
            $sql .= " AND u.user_id = ?";
            $params[] = $filters['user'];
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND u.id IN (SELECT user_id FROM `order` WHERE `order`.user_id = u.id AND `order`.day BETWEEN ? AND ?)";
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
        } 

        return $this->db->fetchAll($sql, $params);
    }
    

    public function getAllByOffice($office)
    {
        if (empty($office)) {
            return array();
        }
        return $this->db->fetchAll(
            "SELECT * FROM user WHERE role = ? AND office_id = ?", 
            array('user', $office)
        );
    }

    function save($data = array())
    {
        $user = new User($data);
        $this->db->insert("user", $user->getArray());
        $user->id = $this->db->lastInsertId();

        return $user->getArray();
    }

    function update($data = array())
    {
        $user = new User($data);
        $this->db->update('user', $user->getArray(), ['id' => $user->id]);

        return $user->getArray();
    }

    function updatePersonalData($data = array())
    {
        return $this->db->update('user', $data, ['id' => $data['id']]);
    }

    function delete($id)
    {
        return $this->db->delete("user", array("id" => $id));
    }

}

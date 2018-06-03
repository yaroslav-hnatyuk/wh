<?php

namespace App\Services;
use App\Entities\Office;

class OfficesService extends BaseService
{

    public function getOne($id)
    {
        $data =$this->db->fetchAssoc("SELECT * FROM office WHERE id=?", [(int) $id]);
        $office = new Office($data);

        return $office->getArray();
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM office");
    }

    public function getAllByCompany($company)
    {
        if (empty($company)) {
            return array();
        }

        return $this->db->fetchAll("SELECT * FROM office WHERE company_id = ?", array($company));
    }

    function save($data = array())
    {
        $office = new Office($data);
        $this->db->insert("office", $office->getArray());
        $office->id = $this->db->lastInsertId();

        return $office->getArray();
    }

    function update($data = array())
    {
        $office = new Office($data);
        $this->db->update('office', $office->getArray(), ['id' => $office->id]);

        return $office->getArray();
    }

    function delete($id)
    {
        return $this->db->delete("office", array("id" => $id));
    }

}

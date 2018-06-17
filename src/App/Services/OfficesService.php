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

    public function findOne($id)
    {
        return $this->db->fetchAssoc("SELECT * FROM office WHERE id=?", [(int) $id]);
    }

    public function getOneByUid($uid)
    {
        $data = $this->db->fetchAssoc("SELECT * FROM office WHERE `uid`=?", [$uid]);
        if (!$data) return null;

        return new Office($data);
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
        $office->uid = hash('ripemd160', $office->address . uniqid());
        $officeData = $office->getArray();
        unset($officeData['id']);

        $this->db->insert("office", $officeData);
        $office->id = $this->db->lastInsertId();

        return $office->getArray();
    }

    public function saveOffices($offices)
    {
        $this->db->beginTransaction();
        try {
            foreach ($offices as &$office) {
                if ($office['id'] && $this->findOne($office['id'])) {
                    $this->db->update('office', array('address' => $office['address']), ['id' => $office['id']]);
                } else {
                    $office = $this->save($office);
                }
            }
            unset($office);
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
        
        return $offices;
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

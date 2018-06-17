<?php

namespace App\Services;
use App\Entities\Company;

class CompaniesService extends BaseService
{

    public function getOne($id)
    {
        $data =$this->db->fetchAssoc("SELECT * FROM company WHERE id=?", [(int) $id]);
        $company = new Company($data);

        return $company->getArray();
    }
    
    public function findOne($id)
    {
        return $this->db->fetchAssoc("SELECT * FROM company WHERE id=?", [(int) $id]);
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM company");
    }

    public function getAllGroupedById()
    {
        $result = array();
        $data = $this->getAll();
        foreach ($data as $company) {
            $company['offices'] = array();
            $result[$company['id']] = $company;
        }

        return $result;
    }

    function save($data = array())
    {
        $company = new Company($data);
        $this->db->insert("company", $company->getArray());
        $company->id = $this->db->lastInsertId();

        return $company->getArray();
    }

    function update($data = array())
    {
        $company = new Company($data);
        $this->db->update('company', $company->getArray(), ['id' => $company->id]);

        return $company->getArray();
    }

    function delete($id)
    {
        return $this->db->delete("company", array("id" => $id));
    }

}

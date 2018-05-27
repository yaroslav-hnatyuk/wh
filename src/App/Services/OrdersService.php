<?php

namespace App\Services;
use App\Entities\Order;

class OrdersService extends BaseService
{

    public function getOne($id)
    {
        $data = $this->db->fetchAssoc("SELECT * FROM `order` WHERE id=?", [(int) $id]);
        $order = new Order($data);

        return $order->getArray();
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM `order`");
    }

    function save($data = array())
    {
        $order = new Order($data);
        $this->db->insert("`order`", $order->getArray());
        $order->id = $this->db->lastInsertId();

        return $order->getArray();
    }

    function update($data = array())
    {
        $order = new Order($data);
        $this->db->update('`order`', $order->getArray(), ['id' => $order->id]);

        return $order->getArray();
    }

    function delete($id)
    {
        return $this->db->delete("`order`", array("id" => $id));
    }

}

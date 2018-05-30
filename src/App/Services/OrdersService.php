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

    function getCurrentPeriod()
    {
        $monday = strtotime("last monday");
        $monday = date('w', $monday) == date('w') ? $monday + 7*86400 : $monday;
        
        $week = array(
            date("Y-m-d",$monday) => array(
                'day' => date("D",$monday),
                'number' => date("d",$monday)
            )
        );

        for ($i = 1; $i <= 6; $i++) {
            $day = strtotime(date("Y-m-d", $monday). " +{$i} days");
            $week[date("Y-m-d", $day)] = array(
                'day' => date("D",$day),
                'number' => date("d",$day)
            );
        }

        return array(
            'start' => date("M d", $monday),
            'end' => date("M d", $day),
            'week' => $week
        );
    }
}

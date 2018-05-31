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

    function getByMenuAndUserAndDay($userId, $menuId, $day)
    {
        $data = $this->db->fetchAssoc(
            "SELECT * FROM `order` WHERE `user_id`=? AND `menu_dish_id`=? AND `day`=?", 
            array($userId, $menuId, $day)
        );
        $order = new Order($data);

        return $order;
    }

    function save($data = array())
    {
        $order = new Order($data);
        $this->db->insert("`order`", $order->getArray());
        $order->id = $this->db->lastInsertId();

        return $order->getArray();
    }

    function saveUserOrders($userId, $orders)
    {
        foreach ($orders as $orderData) {
            $order = $this->getByMenuAndUserAndDay($userId, $orderData['menu_dish_id'], $orderData['day']);
            if ($order->id !== null) {
                $order->count = $orderData['count'];
                $this->db->update('`order`', $order->getArray(), ['id' => $order->id]);
            } else if ((int)$orderData['count'] > 0) {
                $order = $orderData;
                $order['user_id'] = $userId;
                $this->save($order);
            }
        }
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

    function getUserOrdersByPeriod($userId, $period)
    {
        return $this->db->fetchAll("SELECT * FROM `order` 
            WHERE user_id = ? AND day BETWEEN ? AND ?",
            array($userId, $period['start']['date'], $period['end']['date'])
        );
    }

    function mergeMenuWithOrders($menu, $orders, $period)
    {
        $userOrders = array_combine(
            array_keys($period['items']),
            array_fill(0, count($period['items']), 0)
        );

        foreach ($menu as &$dish) {
            $dish['orders'] = $userOrders;
            foreach($orders as $order) {
                if ($dish['menu_id'] === $order['menu_dish_id']) {
                    $dish['orders'][$order['day']] = $order['count'];
                }
            }
        }

        return $menu;
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
            'start' => array(
                'date' => date("Y-m-d", $monday),
                'day' => date("M d", $monday)
            ),
            'end' => array(
                'date' => date("Y-m-d", $day),
                'day' => date("M d", $day)
            ),
            'items' => $week
        );
    }
}

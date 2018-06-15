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

    function getOrdersByFilters($filters = array())
    {
        $params = array();
        $sql = "SELECT r.* FROM `order` r, `user` u, `office` o 
            WHERE r.user_id = u.id AND u.office_id = o.id";

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
            $sql .= " AND r.user_id = ?";
            $params[] = $filters['user'];
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND r.day BETWEEN ? AND ?";
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
        } 

        return $this->db->fetchAll($sql, $params);
    }

    function mergeMenuWithOrders($menu, $orders, $period)
    {
        $result = array();
        $userOrders = array_combine(
            array_keys($period['items']),
            array_fill(0, count($period['items']), array(
                'menu_id' => null,
                'count' => 0,
                'available' => false
            ))
        );

        $groupedOrders = array();
        foreach($orders as $order) {
            $groupedOrders[$order['menu_dish_id'] . '_day_' . $order['day']] += $order['count'];
            $groupedOrders[$order['menu_dish_id'] . '_users'][$order['user_id']] += $order['count'];
        }

        $totalByDays = array();

        foreach ($menu as $dish) {
            if (!isset($result[$dish['dish_id']])) {
                $result[$dish['dish_id']] = $dish;
                $result[$dish['dish_id']]['total_count'] = 0;
            }

            if (isset($groupedOrders[$dish['menu_id'] . '_users'])) {
                foreach ($groupedOrders[$dish['menu_id'] . '_users'] as $userId => $ordersCount) {
                    $result[$dish['dish_id']]['users'][$userId] += $ordersCount;
                }
            }

            $groupedOrders[$order['dish_id']][$order['user_id']];
            foreach ($userOrders as $date => $orderData) {
                $time = strtotime($date);
                if (!isset($result[$dish['dish_id']]['orders'][$date])) {
                    $result[$dish['dish_id']]['orders'][$date] = $orderData;
                }
                if (!isset($totalByDays[$date])) {
                    $totalByDays[$date] = array(
                        'items' => array(), 
                        'total_count' => 0,
                        'total_price' => 0
                    );
                }
                if (!isset($totalByDays[$date]['items'][$dish['dish_id']])) {
                    $totalByDays[$date]['items'][$dish['dish_id']] = array('count' => 0, 'price' => 0);
                }
                if ($time >= strtotime($dish['start']) && $time <= strtotime($dish['end'])) {
                    $result[$dish['dish_id']]['orders'][$date]['menu_id'] = $dish['menu_id'];
                    $result[$dish['dish_id']]['orders'][$date]['available'] = true;
                    if (isset($groupedOrders[$dish['menu_id'] . '_day_' . $date])) {
                        $totalByDays[$date]['items'][$dish['dish_id']]['count'] += $groupedOrders[$dish['menu_id'] . '_day_' . $date];
                        $totalByDays[$date]['items'][$dish['dish_id']]['price'] += $groupedOrders[$dish['menu_id'] . '_day_' . $date] * $dish['price'];
                        $totalByDays[$date]['total_count'] += $groupedOrders[$dish['menu_id'] . '_day_' . $date];
                        $totalByDays[$date]['total_price'] += $groupedOrders[$dish['menu_id'] . '_day_' . $date] * $dish['price'];
                        $result[$dish['dish_id']]['orders'][$date]['count'] = $groupedOrders[$dish['menu_id'] . '_day_' . $date];
                        $result[$dish['dish_id']]['total_count'] += $groupedOrders[$dish['menu_id'] . '_day_' . $date];
                    }
                }
            }
        }

        $totalPrice = array_reduce($totalByDays, function ($carry, $item){
            $carry += $item['total_price'];
            return $carry;
        }, 0);

        return array(
            $result,
            $totalByDays,
            $totalPrice
        );
    }

    function getPeriodForYearAndWeek($year = null, $weekNumber = null, $period = 'week')
    {
        $weekNumber = !$weekNumber ? (new \DateTime())->format("W") : $weekNumber;
        $year = !$year ? (new \DateTime())->format("Y") : $year;
        $prev = array();
        $next = array();
        
        if ($weekNumber + 1 > 52) {
            $next['week'] = 1;
            $next['year'] = $year + 1;
        } else {
            $next['week'] = $weekNumber + 1;
            $next['year'] = $year;
        }

        if ($weekNumber - 1 == 0) {
            $prev['week'] = 52;
            $prev['year'] = $year - 1;
        } else {
            $prev['week'] = $weekNumber - 1;
            $prev['year'] = $year;
        }


        $weekNumber = str_pad($weekNumber, 2, '0', STR_PAD_LEFT);
        $monday = null;
        $sunday = null;
        
        $week = array();
        for($i = 1; $i <= 7; $i++) {
            $day = strtotime($year."W".$weekNumber.$i);
            if ($i == 1) $monday = $day;
            if ($i == 7) $sunday = $day;

            $week[date("Y-m-d", $day)] = array(
                'day' => date("D", $day),
                'number' => date("d", $day)
            );
        }

        if ($period == '2weeks') {
            for($i = 1; $i <= 7; $i++) {
                $day = strtotime($year."W".($weekNumber + 1).$i);
                if ($i == 7) $sunday = $day;
    
                $week[date("Y-m-d", $day)] = array(
                    'day' => date("D", $day),
                    'number' => date("d", $day)
                );
            }
        }
        
        return array(
            'next' => $next,
            'prev' => $prev,
            'start' => array(
                'date' => date("Y-m-d", $monday),
                'day' => date("M d", $monday)
            ),
            'end' => array(
                'date' => date("Y-m-d", $sunday),
                'day' => date("M d", $sunday)
            ),
            'items' => $week
        );
    }
}

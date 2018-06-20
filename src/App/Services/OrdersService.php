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

    function mergeMenuWithOrders($menu, $orders, $period, $export = false)
    {
        $result = array();
        $userOrders = array_combine(
            array_keys($period['items']),
            array_fill(0, count($period['items']), array(
                'menu_id' => null,
                'count' => 0,
                'available' => false,
                'is_working_day' => true
            ))
        );

        $groupedOrders = array();
        foreach($orders as $order) {
            $groupedOrders[$order['menu_dish_id'] . '_day_' . $order['day']][$order['user_id']] += $order['count'];
            if ($export) $groupedOrders[$order['menu_dish_id'] . '_users'][$order['user_id']] += $order['count'];
        }

        $totalByDaysAndUsers = array();

        foreach ($menu as $dish) {
            if (!isset($result[$dish['dish_id']])) {
                $result[$dish['dish_id']] = $dish;
                $result[$dish['dish_id']]['total_count'] = 0;
            }

            if ($export && isset($groupedOrders[$dish['menu_id'] . '_users'])) {
                foreach ($groupedOrders[$dish['menu_id'] . '_users'] as $userId => $ordersCount) {
                    $result[$dish['dish_id']]['users'][$userId] += $ordersCount;
                }
            }

            foreach ($userOrders as $date => $orderData) {
                $time = strtotime($date);
                if (!isset($result[$dish['dish_id']]['orders'][$date])) {
                    $result[$dish['dish_id']]['orders'][$date] = $orderData;
                }
                if (!isset($totalByDaysAndUsers[$date])) {
                    $totalByDaysAndUsers[$date] = array();
                }
                $result[$dish['dish_id']]['orders'][$date]['is_working_day'] = $this->isWorkingDay($date, date('D', strtotime($date)));
                if ($time >= strtotime($dish['start']) && $time <= strtotime($dish['end'])) {
                    $result[$dish['dish_id']]['orders'][$date]['menu_id'] = $dish['menu_id'];
                    $result[$dish['dish_id']]['orders'][$date]['available'] = true;
                    if (isset($groupedOrders[$dish['menu_id'] . '_day_' . $date]) && is_array($groupedOrders[$dish['menu_id'] . '_day_' . $date]) && !empty($groupedOrders[$dish['menu_id'] . '_day_' . $date])) {
                        $ordersSum = array_sum($groupedOrders[$dish['menu_id'] . '_day_' . $date]);
                        foreach ($groupedOrders[$dish['menu_id'] . '_day_' . $date] as $userId => $userOrdersCount) {
                            if ($dish['is_lunch'] && $userOrdersCount > 0) {
                                $totalByDaysAndUsers[$date][$userId]['lunch']['groups'][$dish['group_id']]['day_count'] += $userOrdersCount;
                                $totalByDaysAndUsers[$date][$userId]['lunch']['groups'][$dish['group_id']]['day_price'] += $userOrdersCount * $dish['price'];
                                if (!isset($totalByDaysAndUsers[$date][$userId]['lunch']['min_order'])) $totalByDaysAndUsers[$date][$userId]['lunch']['min_order'] = $userOrdersCount;
                                if ($totalByDaysAndUsers[$date][$userId]['lunch']['min_order'] > $userOrdersCount) {
                                    $totalByDaysAndUsers[$date][$userId]['lunch']['min_order'] = $userOrdersCount;
                                }
                            }
                            $totalByDaysAndUsers[$date][$userId]['items'][$dish['dish_id']]['count'] += $userOrdersCount;
                            $totalByDaysAndUsers[$date][$userId]['items'][$dish['dish_id']]['price'] += $userOrdersCount * $dish['price'];
                            $totalByDaysAndUsers[$date][$userId]['total_count'] += $userOrdersCount;
                            $totalByDaysAndUsers[$date][$userId]['total_price'] += $userOrdersCount * $dish['price'];
                            $result[$dish['dish_id']]['orders'][$date]['count'] += $userOrdersCount;
                            $result[$dish['dish_id']]['total_count'] += $userOrdersCount;   
                        }
                    }
                }
            }
        }

        $discount = 0.25; // Replace with value for config
        $weeklyDiscount = 0.05; // Replace with value for config
        $lunchGroupsCount = 4; // Replace with value for config
        $workingDays = 5; // Calculate for period

        $totalPriceInfo = array(
            'total_price' => 0,
            'total_price_with_discount' => 0,
            'total_price_discount' => 0,
            'total_weekly_discount' => 0,
            'total_weekly' => array()
        );

        $totalByDays = array();
        $totalByUsers = array();
        // ===== DISCOUNTS ======

        if ($export) {
            foreach ($totalByDaysAndUsers as $day => $users) {
                foreach ($users as $userId => $total) {
                    if (!isset($totalByUsers[$userId])) {
                        $totalByUsers[$userId] = array(
                            'total_price' => 0,
                            'total_price_with_discount' => 0,
                            'total_price_discount' => 0
                        );
                    }
                    $lunchDiscount = 0;
                    if (isset($total['lunch']['groups'])) {
                        if ($lunchGroupsCount === count($total['lunch']['groups'])) {
                            foreach ($total['lunch']['groups'] as $groupId => $orderInfo) {
                                $dishPrice = round($orderInfo['day_price'] / $orderInfo['day_count']);
                                $lunchDiscount += round($dishPrice * $total['lunch']['min_order'] * $discount);
                            }
                            $totalByUsers[$userId]['total_price_with_discount'] += $totalByDaysAndUsers[$day][$userId]['total_price'] - $lunchDiscount;
                            $totalByUsers[$userId]['total_price_discount'] += $lunchDiscount;
                        } else {
                            $totalByUsers[$userId]['total_price_with_discount'] += $totalByDaysAndUsers[$day][$userId]['total_price'];
                        }
                    }
    
                    if ($totalByDaysAndUsers[$day][$userId]['total_count'] > 0) {
                        $w = date("W", strtotime($day));
                        $totalPriceInfo['total_weekly'][$userId][$w]['days_with_orders'] += 1;
                        $totalPriceInfo['total_weekly'][$userId][$w]['discount'] += round($weeklyDiscount * ($totalByDaysAndUsers[$day][$userId]['total_price'] - $lunchDiscount));
                    }
    
                    $totalByUsers[$userId]['total_price'] += $totalByDaysAndUsers[$day][$userId]['total_price'];
                }
    
            }
            unset($totalByDaysAndUsers);
    
            // ===== APPLY WEEKLY DISCOUNT ======
            $workingDaysByWeeks = $this->getWokringDaysForPeriod($period);
            foreach ($workingDaysByWeeks as $week => $workingDaysCount) {
                foreach ($totalPriceInfo['total_weekly'] as $userId => $weeks) {
                    // if (!isset($totalPriceInfo['total_weekly_discount'][$userId])) $totalPriceInfo['total_weekly_discount'][$userId] = 0;
                    if ($workingDaysCount === $weeks[$week]['days_with_orders']) {
                        $totalPriceInfo['total_user_weekly_discount'][$userId] += $weeks[$week]['discount'];
                    }
                }
            }
            unset($totalPriceInfo['total_weekly']);
        } else {
            foreach ($totalByDaysAndUsers as $day => $users) {
                $totalByDays[$day] = array(
                    'total_price' => 0,
                    'total_price_with_discount' => 0,
                    'total_price_discount' => 0,
                    'lunch_count' => 0
                );
                foreach ($users as $userId => $total) {
                    $lunchDiscount = 0;
                    if (isset($total['lunch']['groups'])) {
                        if ($lunchGroupsCount === count($total['lunch']['groups'])) {
                            foreach ($total['lunch']['groups'] as $groupId => $orderInfo) {
                                $dishPrice = round($orderInfo['day_price'] / $orderInfo['day_count']);
                                $lunchDiscount += round($dishPrice * $total['lunch']['min_order'] * $discount);
                            }
                            $totalByDays[$day]['total_price_with_discount'] += $totalByDaysAndUsers[$day][$userId]['total_price'] - $lunchDiscount;
                            $totalByDays[$day]['total_price_discount'] += $lunchDiscount;
                        } else {
                            $totalByDays[$day]['total_price_with_discount'] += $totalByDaysAndUsers[$day][$userId]['total_price'];
                        }
                        $totalByDays[$day]['lunch_count'] += $total['lunch']['min_order'];
                    }
    
                    if ($totalByDaysAndUsers[$day][$userId]['total_count'] > 0) {
                        $w = date("W", strtotime($day));
                        $totalPriceInfo['total_weekly'][$userId][$w]['days_with_orders'] += 1;
                        $totalPriceInfo['total_weekly'][$userId][$w]['discount'] += round($weeklyDiscount * ($totalByDaysAndUsers[$day][$userId]['total_price'] - $lunchDiscount));
                    }
    
                    $totalByDays[$day]['total_price'] += $totalByDaysAndUsers[$day][$userId]['total_price'];
                }
    
                $totalPriceInfo['total_price'] += $totalByDays[$day]['total_price'];
                $totalPriceInfo['total_price_with_discount'] += $totalByDays[$day]['total_price_with_discount'];
                $totalPriceInfo['total_price_discount'] += $totalByDays[$day]['total_price_discount'];
            }
            unset($totalByDaysAndUsers);
    
            // ===== APPLY WEEKLY DISCOUNT ======
            $workingDaysByWeeks = $this->getWokringDaysForPeriod($period);
            foreach ($workingDaysByWeeks as $week => $workingDaysCount) {
                foreach ($totalPriceInfo['total_weekly'] as $userId => $weeks) {
                    if ($workingDaysCount === $weeks[$week]['days_with_orders']) {
                        $totalPriceInfo['total_weekly_discount'] += $weeks[$week]['discount'];
                    }
                }
            }
            unset($totalPriceInfo['total_weekly']);
    
            if ($totalPriceInfo['total_weekly_discount'] > 0) {
                $totalPriceInfo['total_price_with_discount'] -= $totalPriceInfo['total_weekly_discount'];
            }
        }

        return array(
            $result,
            $export ? $totalByUsers : $totalByDays,
            $totalPriceInfo
        );
    }

    function getWokringDaysForPeriod($period)
    {
        $result = array();
        foreach ($period['items'] as $date => $value) {
            $w = date("W", strtotime($date));
            if ($this->isWorkingDay($date, $value['day'])) {
                $result[$w] += 1;
            }
        }

        return $result;
    }

    function isWorkingDay($date, $day)
    {
        if ($day == 'Sun' || $day == 'Sat') {
            return false;
        }
        return true;
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


        $monday = null;
        $sunday = null;
        
        $week = array();
        for($i = 1; $i <= 7; $i++) {
            $day = strtotime($year."W".str_pad($weekNumber, 2, '0', STR_PAD_LEFT).$i);
            if ($i == 1) $monday = $day;
            if ($i == 7) $sunday = $day;

            $week[date("Y-m-d", $day)] = array(
                'day' => date("D", $day),
                'number' => date("d", $day),
                'is_working_day' => $this->isWorkingDay(date("Y-m-d", $day), date('D', $day))
            );
        }

        if ($period == '2weeks') {
            for($i = 1; $i <= 7; $i++) {
                $day = strtotime($year."W".str_pad($weekNumber + 1, 2, '0', STR_PAD_LEFT).$i);
                if ($i == 7) $sunday = $day;
    
                $week[date("Y-m-d", $day)] = array(
                    'day' => date("D", $day),
                    'number' => date("d", $day),
                    'is_working_day' => $this->isWorkingDay(date("Y-m-d", $day), date('D', $day))
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












    // DEPRECATED
    // function mergeMenuWithOrders($menu, $orders, $period)
    // {
    //     $result = array();
    //     $userOrders = array_combine(
    //         array_keys($period['items']),
    //         array_fill(0, count($period['items']), array(
    //             'menu_id' => null,
    //             'count' => 0,
    //             'available' => false
    //         ))
    //     );

    //     $groupedOrders = array();
    //     foreach($orders as $order) {
    //         $groupedOrders[$order['menu_dish_id'] . '_day_' . $order['day']] += $order['count'];
    //     }

    //     $totalByDays = array();

    //     foreach ($menu as $dish) {
    //         if (!isset($result[$dish['dish_id']])) {
    //             $result[$dish['dish_id']] = $dish;
    //             $result[$dish['dish_id']]['total_count'] = 0;
    //         }

    //         $groupedOrders[$order['dish_id']][$order['user_id']];
    //         foreach ($userOrders as $date => $orderData) {
    //             $time = strtotime($date);
    //             if (!isset($result[$dish['dish_id']]['orders'][$date])) {
    //                 $result[$dish['dish_id']]['orders'][$date] = $orderData;
    //             }
    //             if (!isset($totalByDays[$date])) {
    //                 $totalByDays[$date] = array(
    //                     'items' => array(),
    //                     'lunch' => array(),
    //                     'total_count' => 0,
    //                     'total_price' => 0
    //                 );
    //             }
    //             if (!isset($totalByDays[$date]['items'][$dish['dish_id']])) {
    //                 $totalByDays[$date]['items'][$dish['dish_id']] = array('count' => 0, 'price' => 0);
    //             }
    //             if ($time >= strtotime($dish['start']) && $time <= strtotime($dish['end'])) {
    //                 $result[$dish['dish_id']]['orders'][$date]['menu_id'] = $dish['menu_id'];
    //                 $result[$dish['dish_id']]['orders'][$date]['available'] = true;
    //                 if (isset($groupedOrders[$dish['menu_id'] . '_day_' . $date])) {
    //                     if ($dish['is_lunch'] && $groupedOrders[$dish['menu_id'] . '_day_' . $date] > 0) {
    //                         $totalByDays[$date]['lunch']['groups'][$dish['group_id']]['day_count'] += $groupedOrders[$dish['menu_id'] . '_day_' . $date];
    //                         $totalByDays[$date]['lunch']['groups'][$dish['group_id']]['day_price'] += $groupedOrders[$dish['menu_id'] . '_day_' . $date] * $dish['price'];
    //                         if (!isset($totalByDays[$date]['lunch']['min_order'])) $totalByDays[$date]['lunch']['min_order'] = $groupedOrders[$dish['menu_id'] . '_day_' . $date];
    //                         if ($totalByDays[$date]['lunch']['min_order'] > $groupedOrders[$dish['menu_id'] . '_day_' . $date]) {
    //                             $totalByDays[$date]['lunch']['min_order'] = $groupedOrders[$dish['menu_id'] . '_day_' . $date];
    //                         }
    //                     }
    //                     $totalByDays[$date]['items'][$dish['dish_id']]['count'] += $groupedOrders[$dish['menu_id'] . '_day_' . $date];
    //                     $totalByDays[$date]['items'][$dish['dish_id']]['price'] += $groupedOrders[$dish['menu_id'] . '_day_' . $date] * $dish['price'];
    //                     $totalByDays[$date]['total_count'] += $groupedOrders[$dish['menu_id'] . '_day_' . $date];
    //                     $totalByDays[$date]['total_price'] += $groupedOrders[$dish['menu_id'] . '_day_' . $date] * $dish['price'];
    //                     $result[$dish['dish_id']]['orders'][$date]['count'] = $groupedOrders[$dish['menu_id'] . '_day_' . $date];
    //                     $result[$dish['dish_id']]['total_count'] += $groupedOrders[$dish['menu_id'] . '_day_' . $date];
    //                 }
    //             }
    //         }
    //     }

    //     $discount = 0.25; // Replace with value for config
    //     $weeklyDiscount = 0.05; // Replace with value for config
    //     $lunchGroupsCount = 4; // Replace with value for config
    //     $workingDays = 5; // Calculate for period

    //     $totalPriceInfo = array(
    //         'total_price' => 0,
    //         'total_price_with_discount' => 0,
    //         'total_price_discount' => 0,
    //         'total_weekly_discount' => 0,
    //         'total_weekly' => array()
    //     );
    //     // ===== DISCOUNTS ======
    //     foreach ($totalByDays as $day => $total) {
    //         if (isset($total['lunch']['groups'])) {
    //             if ($lunchGroupsCount === count($total['lunch']['groups'])) {
    //                 $lunchDiscount = 0;
    //                 foreach ($total['lunch']['groups'] as $groupId => $orderInfo) {
    //                     $dishPrice = round($orderInfo['day_price'] / $orderInfo['day_count']);
    //                     $lunchDiscount += round($dishPrice * $total['lunch']['min_order'] * $discount);
    //                 }
    //                 $totalByDays[$day]['total_price_with_discount'] = $totalByDays[$day]['total_price'] - $lunchDiscount;
    //                 $totalByDays[$day]['total_price_discount'] = $lunchDiscount;
    //             } else {
    //                 $totalByDays[$day]['total_price_with_discount'] = $totalByDays[$day]['total_price'];
    //                 $totalByDays[$day]['total_price_discount'] = 0;
    //             }
    //         }

    //         if ($totalByDays[$day]['total_count'] > 0) {
    //             $w = date("W", strtotime($day));
    //             $totalPriceInfo['total_weekly'][$w]['days_with_orders'] += 1;
    //             $totalPriceInfo['total_weekly'][$w]['discount'] += round($weeklyDiscount * $totalByDays[$day]['total_price_with_discount']);
    //         }

    //         $totalPriceInfo['total_price'] += $totalByDays[$day]['total_price'];
    //         $totalPriceInfo['total_price_with_discount'] += $totalByDays[$day]['total_price_with_discount'];
    //         $totalPriceInfo['total_price_discount'] += $totalByDays[$day]['total_price_discount'];
    //     }

    //     // ===== APPLY WEEKLY DISCOUNT ======
    //     $workingDaysByWeeks = $this->getWokringDaysForPeriod($period);
    //     foreach ($workingDaysByWeeks as $week => $workingDaysCount) {
    //         if ($workingDaysCount === $totalPriceInfo['total_weekly'][$week]['days_with_orders']) {
    //             $totalPriceInfo['total_weekly_discount'] += $totalPriceInfo['total_weekly'][$week]['discount'];
    //         }    
    //     }

    //     if ($totalPriceInfo['total_weekly_discount'] > 0) {
    //         $totalPriceInfo['total_price_with_discount'] -= $totalPriceInfo['total_weekly_discount'];
    //     }

    //     return array(
    //         $result,
    //         $totalByDays,
    //         $totalPriceInfo
    //     );
    // }
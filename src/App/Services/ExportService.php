<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WriterXlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExportService extends BaseService
{
    public function createXlsReport($menu, $users, $totalByDaysAndUsers, $totalPriceInfo, $filters)
    {
        $gUsers = array();
        foreach ($users as $user) {
            $gUsers[$user['id']] = array(
                'user_id' => $user['id'],
                'name' => "{$user['first_name']} {$user['last_name']}",
                'email' => $user['email'],
                'ipn' => $user['ipn']
            );
        }

        $gGroups = array();
        foreach ($menu as $item) {
            $gGroups[$item['group_id']] = array(
                'group_id' => $item['group_id'],
                'group_name' => $item['group_name']
            );
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Walnut House')
            ->setLastModifiedBy('Walnut House')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');


        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('B2', 'Компанія');
        $spreadsheet->getActiveSheet()->setCellValue("B3", $filters['company']['name']);

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('D2', 'Офіс');
        if ($filters['office']) {
            $spreadsheet->getActiveSheet()->setCellValue("D3", $filters['office']['address']);
        } else {
            $spreadsheet->getActiveSheet()->setCellValue("D3", 'Всі');
        }
        
        $spreadsheet->setActiveSheetIndex(0)
            ->mergeCells('F2:G2')
            ->setCellValue('F2', 'Період');
        $spreadsheet->getActiveSheet()->setCellValue("F3", $filters['start_date']);
        $spreadsheet->getActiveSheet()->setCellValue("G3", $filters['end_date']);

        $rowNumber = 5;
        foreach ($totalByDaysAndUsers as $date => $usersOrders) {
            if (count($usersOrders)) {
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue("A{$rowNumber}", $date);
                $columnNumber = 2;
                foreach ($gGroups as $group) {
                    $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}{$rowNumber}", $group['group_name']);
                    $columnNumber++;
                }
                $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}{$rowNumber}", 'Сума');
                $rowNumber++;

                foreach ($usersOrders as $userId => $orders) {
                    $spreadsheet->getActiveSheet()->setCellValue("A{$rowNumber}", $gUsers[$userId]['name']);
                    $columnNumber = 2;
                    $ordersPrice = 0;
                    foreach ($gGroups as $group) {
                        $count = isset($orders['items'][$group['group_id']]) ? (int)$orders['items'][$group['group_id']]['count'] : 0;
                        $price = isset($orders['items'][$group['group_id']]) ? (int)$orders['items'][$group['group_id']]['price'] : 0;
                        $dessert = isset($orders['items'][$group['group_id']]) ? (int)$orders['items'][$group['group_id']]['dessert'] : 0;
                        $ordersPrice += $price;
                        $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}{$rowNumber}", $count);
                        if ($dessert) {
                            $spreadsheet->getActiveSheet()->getComment("{$this->columnLetter($columnNumber)}{$rowNumber}")->getText()->createTextRun('з десертом');
                        } else {
                            $spreadsheet->getActiveSheet()->getComment("{$this->columnLetter($columnNumber)}{$rowNumber}")->getText()->createTextRun('з салатом');
                        }
                        $columnNumber++;
                    }
                    $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}{$rowNumber}", $ordersPrice);
                    $rowNumber++;
                }
                $rowNumber += 2;
            }
        }

        $spreadsheet->getActiveSheet()->setTitle('Report');
        $spreadsheet->setActiveSheetIndex(0);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"walnut-house-report-{$filters['start_date']}-{$filters['end_date']}.xlsx\"");
        header('Cache-Control: max-age=0');
        
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = new WriterXlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }

    function createXlsReportWeekly($users, $orders, $period, $filters) {
        $gUsers = array();
        foreach ($users as $user) {
            $gUsers[$user['id']] = array(
                'user_id' => $user['id'],
                'name' => "{$user['first_name']} {$user['last_name']}",
                'email' => $user['email'],
                'ipn' => $user['ipn']
            );
        }

        $userOrders = array_combine(
            array_keys($period['items']),
            array_fill(0, count($period['items']), 0)
        );

        $result = array();
        foreach ($orders as $order) {
            if (!isset($result[$order['user_id']])) {
                $result[$order['user_id']] = $gUsers[$order['user_id']];
                $result[$order['user_id']]['orders'] = $userOrders;
            }
            if (isset($result[$order['user_id']]['orders'][$order['day']])) {
                $result[$order['user_id']]['orders'][$order['day']] += $order['count'] * 100;
            }
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Walnut House')
            ->setLastModifiedBy('Walnut House')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');


        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('B2', 'Компанія');
        $spreadsheet->getActiveSheet()->setCellValue("B3", $filters['company']['name']);

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('D2', 'Офіс');
        if ($filters['office']) {
            $spreadsheet->getActiveSheet()->setCellValue("D3", $filters['office']['address']);
        } else {
            $spreadsheet->getActiveSheet()->setCellValue("D3", 'Всі');
        }
        
        $spreadsheet->setActiveSheetIndex(0)
            ->mergeCells('F2:G2')
            ->setCellValue('F2', 'Період');
        $spreadsheet->getActiveSheet()->setCellValue("F3", $filters['start_date']);
        $spreadsheet->getActiveSheet()->setCellValue("G3", $filters['end_date']);

        $rowNumber = 5;
        $columnNumber = 2;
        $spreadsheet->setActiveSheetIndex(0)->setCellValue("{$this->columnLetter(1)}{$rowNumber}", "Користувачі");
        foreach ($userOrders as $date => $count) {
            $spreadsheet->setActiveSheetIndex(0)->setCellValue("{$this->columnLetter($columnNumber)}{$rowNumber}", $date);
            $columnNumber++;
        }

        $rowNumber = 6;
        foreach ($result as $user) {
            $columnNumber = 1;
            $spreadsheet->setActiveSheetIndex(0)->setCellValue("{$this->columnLetter($columnNumber)}{$rowNumber}", $user['name']);
            foreach ($user['orders'] as $price) {
                $columnNumber++;
                $spreadsheet->setActiveSheetIndex(0)->setCellValue("{$this->columnLetter($columnNumber)}{$rowNumber}", $price);
            }
            $rowNumber++;
        }

        $spreadsheet->getActiveSheet()->setTitle('Report');
        $spreadsheet->setActiveSheetIndex(0);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"company-weekly-report-{$filters['start_date']}-{$filters['end_date']}.xlsx\"");
        header('Cache-Control: max-age=0');
        
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = new WriterXlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }

    function columnLetter($c){

        $c = intval($c);
        if ($c <= 0) return '';
    
        $letter = '';
        while($c != 0){
           $p = ($c - 1) % 26;
           $c = intval(($c - $p) / 26);
           $letter = chr(65 + $p) . $letter;
        }
        
        return $letter;
    }

}

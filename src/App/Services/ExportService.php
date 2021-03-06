<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
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

        $gDishes = array();
        foreach ($menu as $item) {
            $gDishes[$item['dish_id']] = array(
                'dish_id' => $item['dish_id'],
                'dish_name' => $item['dish_name'],
                'group_id' => $item['group_id'],
                'group_name' => $item['group_name']
            );
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Walnut House')
            ->setLastModifiedBy('Walnut House')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
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
                foreach ($gDishes as $dish) {
                    $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}{$rowNumber}", $dish['dish_name']);
                    $columnNumber++;
                }
                $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}{$rowNumber}", 'Сума');
                $rowNumber++;

                foreach ($usersOrders as $userId => $orders) {
                    $spreadsheet->getActiveSheet()->setCellValue("A{$rowNumber}", $gUsers[$userId]['name']);
                    $columnNumber = 2;
                    $ordersPrice = 0;
                    foreach ($gDishes as $dish) {
                        $count = isset($orders['items'][$dish['dish_id']]) ? (int)$orders['items'][$dish['dish_id']]['count'] : 0;
                        $price = isset($orders['items'][$dish['dish_id']]) ? (int)$orders['items'][$dish['dish_id']]['price'] : 0;
                        $ordersPrice += $price;
                        $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}{$rowNumber}", $count);
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
        
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"walnut-house-report-{$filters['start_date']}-{$filters['end_date']}.xls\"");
        header('Cache-Control: max-age=0');
        
        header('Cache-Control: max-age=1');
        
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
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

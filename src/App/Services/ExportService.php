<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExportService extends BaseService
{
    public function createXlsReport($menu, $users, $totalByUsers, $totalPriceInfo, $filters)
    {
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


        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A5', 'Користувач');

        $columnNumber = 2;
        foreach ($menu as $item) {
            $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}5", $item['dish_name']);
            $columnNumber++;
        }
        $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}5", 'Сума без знижок');
        $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber + 1)}5", 'Знажка за комплексні');
        $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber + 2)}5", 'Знижка за тижневі замовлення');
        $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber + 3)}5", 'Кінцева сума');

        $usersCount = 6;
        foreach ($users as $user) {
            $columnNumber = 2;
            $spreadsheet->getActiveSheet()->setCellValue("A{$usersCount}", $user['first_name'] . " " . $user['last_name']);
            
            $totalPrice = 0;
            foreach ($menu as $dish) {
                $ordersCount = isset($dish['users'][$user['id']]) ? $dish['users'][$user['id']] : 0;
                $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}{$usersCount}", $ordersCount);
                $totalPrice += $ordersCount * $dish['price'];
                $columnNumber++;
            }

            $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}{$usersCount}", $totalByUsers[$user['id']]['total_price']);
            $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber + 1)}{$usersCount}", $totalByUsers[$user['id']]['total_price_discount']);
            $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber + 2)}{$usersCount}", $totalPriceInfo['total_user_weekly_discount'][$user['id']]);
            $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber + 3)}{$usersCount}", $totalByUsers[$user['id']]['total_price_with_discount'] - $totalPriceInfo['total_user_weekly_discount'][$user['id']]);
            $usersCount++;
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

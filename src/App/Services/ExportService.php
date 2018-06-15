<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExportService extends BaseService
{
    private $columns = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

    public function createReport($menu, $users)
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
            ->setCellValue('A1', 'Користувач');

        $columnNumber = 2;
        foreach ($menu as $item) {
            $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}1", $item['dish_name']);
            $columnNumber++;
        }

        $usersCount = 2;
        foreach ($users as $user) {
            $columnNumber = 2;
            $spreadsheet->getActiveSheet()->setCellValue("A{$usersCount}", $user['first_name'] . " " . $user['last_name']);
            foreach ($menu as $dish) {
                $ordersCount = isset($dish['users'][$user['id']]) ? $dish['users'][$user['id']] : 0;
                $spreadsheet->getActiveSheet()->setCellValue("{$this->columnLetter($columnNumber)}{$usersCount}", $ordersCount);
                $columnNumber++;
            }
            $usersCount++;
        }

        $spreadsheet->getActiveSheet()->setTitle('Report');
        $spreadsheet->setActiveSheetIndex(0);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="01simple.xls"');
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

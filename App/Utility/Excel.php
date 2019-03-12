<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/8 0008
 * Time: 15:18
 */

namespace App\Utility;


use EasySwoole\Http\Response;

class Excel
{
    /**
     * 导出excel表格
     * exportExcel
     * @param Response $response
     * @param          $list
     * @param          $indexKey
     * @param string   $filename
     * @param int      $startRow
     * @param bool     $excel2007
     * @return bool
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Writer_Exception
     * @author Tioncico
     * Time: 15:42
     */
    static function exportExcel(Response $response, $list, $indexKey, $filename='', $startRow = 1, $excel2007 = false)
    {
        if (!is_array($indexKey)) return false;
        if (empty($filename)) $filename = time();

        $header_arr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        //初始化PHPExcel()
        $objPHPExcel = new \PHPExcel();

        //设置保存版本格式
        if ($excel2007) {
            $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
            $filename = $filename . '.xlsx';
        } else {
            $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
            $filename = $filename . '.xls';
        }

        //接下来就是写数据到表格里面去
        $objActSheet = $objPHPExcel->getActiveSheet();
        //$startRow = 1;
        foreach ($list as $row) {
            foreach ($indexKey as $key => $value) {
                //这里是设置单元格的内容
                $objActSheet->setCellValue($header_arr[$key] . $startRow, $row[$value]);
            }
            $startRow++;
        }

        $response->withHeader("Pragma", " public");
        $response->withHeader("Expires", " 0");
        $response->withHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
        $response->withHeader("Content-Type", "application/force-download");
        $response->withHeader("Content-Type", "application/vnd.ms-execl");
        $response->withHeader("Content-Type", "application/octet-stream");
        $response->withHeader("Content-Type", "application/download");;
        $response->withHeader('Content-Disposition','attachment;filename=' . $filename . '');
        $response->withHeader("Content-Transfer-Encoding", "binary");
        ob_start();
        $objWriter->save('php://output');
        $content = ob_get_clean();
        $response->write($content);
        $response->end();
    }
}
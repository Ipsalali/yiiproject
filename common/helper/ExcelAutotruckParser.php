<?php 

namespace common\helper;

use Yii;
use common\models\AutotruckImport;
use phpoffice\phpexcel\Classes\PHPExcel;
use common\helper\BaseArrayHelper;

class ExcelAutotruckParser{




	public static function parse(AutotruckImport $file){

        $fileName = Yii::getAlias("@backend")."/tmp/aut_".$file->id.".".$file->extension;
        $f = fopen($fileName, "w");
        fwrite($f,$file->fileBinary);
        fclose($f);
        
        // ini_set('memory_limit', '256M');

        $objReader = \PHPExcel_IOFactory::createReader("Excel2007");
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($fileName);
		$sheets = $objPHPExcel->getSheetNames();
		
		// PHPExcel_Worksheet
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$sheet_name = $objWorksheet->getTitle();
		$sheet_name = mb_strtolower($sheet_name);

		$highestDataRow = $objWorksheet->getHighestRow(); 
		
		$highestDataColumn = $objWorksheet->getHighestColumn(); 

		$data = [];
		for ($row=0; $row < $highestDataRow; $row++) { 
			for ($col=0; $col < $highestDataColumn; $col++) {
				$data[$sheet_name][$row][$col] = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
			}
		}


		print_r($data);
		exit;

		// $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
		$sheets_names = [];
		foreach ($sheets as $key => $name) {
			$sheet = mb_strtolower($name);
			array_push($sheets_names, $sheet);
		}
		

		print_r($sheets_names);
		exit;
	}

}
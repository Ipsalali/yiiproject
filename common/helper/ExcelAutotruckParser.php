<?php 

namespace common\helper;

use Yii;
use common\models\AutotruckImport;
use phpoffice\phpexcel\Classes\PHPExcel;
use common\helper\BaseArrayHelper;

class ExcelAutotruckParser{




	public static function parse(AutotruckImport $file){

        $fileName = Yii::getAlias("@common")."/tmp/aut_".$file->id.".".$file->extension;
        $f = fopen($fileName, "w");
        fwrite($f,$file->fileBinary);
        fclose($f);
        

        $objReader = \PHPExcel_IOFactory::createReader("Excel2007");
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($fileName);
		
		

		$sheets = $objPHPExcel->getAllSheets();
		$data = [];
		foreach ($sheets as $key => $sheet) {
			$titles = self::getTitles($sheet);
			$sheet_name = $sheet->getTitle();
			$sheet_name = mb_strtolower($sheet_name);

			$collsCount = count($titles);
			$data[$sheet_name]['titles'] = $titles;
			
			$highestDataRow = $sheet->getHighestRow();

			for ($row=2; $row < $highestDataRow; $row++) {
				$rowData = [];
				
				for ($col=0; $col < $collsCount; $col++) {
					$rowData[$titles[$col]] = $sheet->getCellByColumnAndRow($col, $row)->getValue();
				}

				if(count($rowData) && implode("", $rowData) != ""){
					$data[$sheet_name][] = $rowData;
				}
			}
		}
		
		return $data;
	}



	public static function getTitles(\PHPExcel_Worksheet $sheet){

		$highestDataColumn = $sheet->getHighestDataColumn(1);
		$highestDataColumn = strlen($highestDataColumn) > 1 ? "Z" : $highestDataColumn;
		
		$titles = [];
		$titlesWithEmpty = [];
		for ($char="A"; $char <= $highestDataColumn; $char++) {
			$coord = $char."1";
			$title = $sheet->getCell($coord)->getValue();
			$title = trim(strip_tags($title));
			$title = mb_strtolower($title);
			if($title == "" || empty($title)){
				$titlesWithEmpty[] = $title;
			}else{
				$titlesWithEmpty[] = $title;
				$titles = $titlesWithEmpty;
			}
		}

		return $titles;
	}
}
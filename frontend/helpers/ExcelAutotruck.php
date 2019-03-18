<?php

namespace frontend\helpers;

use phpoffice\phpexcel\Classes\PHPExcel;
use frontend\models\App;
use frontend\models\Autotruck;
use common\models\Organisation;
use common\models\Client;
use Yii;
use yii\helpers\Html;

class ExcelAutotruck{

	public $startRow = 3;
	public $startCell = 3;
	public $curRow = 5;
	public $curCell = 3;

	public $objPHPExcel;
	public $styleArray = array(
			'borders' => array(
				'outline'=> array(
					'style' => \PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000')
				)
			)
		);

	public $font = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 11
    ));

	public $border_bottom = array(
			'borders' => array(
				'bottom' => array(
					'style' => \PHPExcel_Style_Border::BORDER_THICK,
					'color' => array('rgb' => '000000')
				)
			)
		);


	public $font_bold = array(
		'font'=>array(
			'bold'=>true,
			'size'=>19
		)
	);

	public  $styles = array(
			'border-thick' =>array(
				'borders'=>array('outline'=>array('style'=>\PHPExcel_Style_Border::BORDER_THICK,'color'=>array('rgb'=>'000000')))
			),

			'font_mini' =>array(
				'font'=>array('size'=>12)),

			'font_bold' => array(
				'font'=>array(
					'bold'=>true
				)
			) 
		);


	public function __construct(){

		$this->objPHPExcel = new \PHPExcel();
		$this->setProperties();
	}

	public function setProperties(){
		if(!is_object($this->objPHPExcel)){
			$this->objPHPExcel = new \PHPExcel();
		}

		$this->objPHPExcel->getProperties()->setCreator("Ted organisation")
							 ->setLastModifiedBy("Ted")
							 ->setTitle("Check document")
							 ->setSubject("Check document")
							 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Check for client");
	}

	public function setHead($autotruck){

		$this->setText("Инвойс: ".$autotruck->name,"B$this->startRow",1,0,30);
		$this->merge("B$this->startRow:P7");
		$this->objPHPExcel->getActiveSheet()->getStyle("B$this->startRow:P7")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$this->objPHPExcel->getActiveSheet()->getStyle("B$this->startRow:P7")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$this->objPHPExcel->getActiveSheet()->getStyle("B$this->startRow:P7")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		$this->objPHPExcel->getActiveSheet()->getStyle("B$this->startRow:P7")->getFill()->getStartColor()->setARGB('337ab7FF');

		$this->curRow = 9;
		$this->setMetaData($autotruck);
	}

	public function setMetaData($autotruck){

		$localStart = $this->curRow;

		$this->setText("Дата:","B$this->curRow",1);
		$this->setText(date("d.m.Y",strtotime($autotruck->date)),"C$this->curRow");

		$this->setText("Курс:","E$this->curRow",1);
		// $s = sprintf("%.2f", $a->summa_us);
		$this->setText(round($autotruck->course,4),"F$this->curRow");

		$this->setText("Страна поставки:","H$this->curRow",1);
		$this->merge("H$this->curRow:I$this->curRow");
		$cname = $autotruck->countryName ? $autotruck->countryName : "Не указан";
		$this->setText($cname,"J$this->curRow");

		$this->setText("Статус:","L$this->curRow",1);
		$status = $autotruck->activeStatus ? $autotruck->activeStatus->title : "Не указан";
		$this->setText($status,"M$this->curRow");
		
		if($autotruck->activeStatusTrace->trace_id){
			$d = date("d.m.Y",strtotime($autotruck->activeStatusTrace->trace_id));
			$this->setText($d,"O$this->curRow");
			
		}

		$this->curRow +=2;

		$this->setText("Комментарии:","B$this->curRow",1);
		$this->merge("B$this->curRow:C$this->curRow");
		if($autotruck->description !=''){
			$this->setText($autotruck->description,"D$this->curRow");
		}
		
		$cRow = $this->curRow;
		$this->curRow +=2;
		$this->merge("D$cRow:O$this->curRow");
		$this->objPHPExcel->getActiveSheet()->getStyle("D$cRow:O$this->curRow")->applyFromArray($this->styleArray);
		$this->objPHPExcel->getActiveSheet()->getStyle("D$cRow:O$this->curRow")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);

		$this->curRow +=2;
		$localEnd = $this->curRow;
		$borderMerge = "B$localStart:P$localEnd";
		$this->objPHPExcel->getActiveSheet()->getStyle($borderMerge)->applyFromArray($this->styleArray);
	}

	
	public function setText($string,$coords,$bold=null,$color=0,$size=0,$wrap=0,$vertical=0){

		$titleName = new \PHPExcel_RichText();
		$style = $titleName->createTextRun($string);
		if($bold){
			$style->getFont()->setBold(true);
		}
		if($color){
			$style->getFont()->setColor( new \PHPExcel_Style_Color( $color ) );
		}
		if($size){
			$style->getFont()->setSize($size);
		}
		if($vertical){
			// $style->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		}
		if($wrap){
			//print_r($style->getAlignment());
			// $style->getAlignment()->setWrapText(true);
		}
		
		$this->objPHPExcel->getActiveSheet()->getCell($coords)->setValue($titleName);

	}

	public function setString($value,$coords){
		

		$this->objPHPExcel->getActiveSheet()->getCell($coords)->setValueExplicit($value, \PHPExcel_Cell_DataType::TYPE_STRING2);

	}

	public function setFloat($value,$coords){
		$this->objPHPExcel->getActiveSheet()->getCell($coords)->setValueExplicit($value, \PHPExcel_Cell_DataType::TYPE_NUMERIC);
	}

	public function merge($coords){
		$this->objPHPExcel->getActiveSheet()->mergeCells($coords);
	}

	public function setProducts($apps){
		if(!is_array($apps) || !count($apps)) return false;

		$this->curRow +=2;
		$startRow = $this->curRow;
		$endRow = $startRow;
		
		$this->setText("№","B$startRow",1);

		$this->setText("Клиент","C$startRow",1);
		$this->merge("C$startRow:D$startRow");

		$this->setText("Товары (работы, услуги)","E$startRow",1);

		$this->merge("E$startRow:G$startRow");
		$this->setText("Кол-во","H$startRow",1);

		$this->setText("Ед.","I$startRow",1);

		$this->setText("Ставка","J$startRow",1);

		$this->merge("J$startRow:K$startRow");
		$this->setText("Сумма","L$startRow",1);

		$this->merge("L$startRow:M$startRow");

		$this->setText("Комментарии","N$startRow",1);
		$this->merge("N$startRow:P$startRow");
		

		$totalW = 0;
		$total = 0;
		foreach ($apps as $key => $a) {
			$endRow++;

			$B="B$endRow";
			$i = sprintf("%d",$key+1);
			$this->setFloat($i,$B);

			//Артикул пропускаем
			$C="C$endRow";
			$cl_name = ($a->client && $a->buyer->name) ? $a->buyer->name : 'Не указан';
			$this->setString($cl_name,$C,0,0,12);
			$Dmerge = "C$endRow:D$endRow";
			$this->merge($Dmerge);

			//Наименование
			$D="E$endRow";
			$this->setString($a->description,$D,0,0,12);
			$Dmerge = "E$endRow:G$endRow";
			$this->objPHPExcel->getActiveSheet()->mergeCells($Dmerge);

			//Кол-во
			$H = "H$endRow";
			$weight = $a->weight;//sprintf("%.2f", $a->weight);
		
			$this->setFloat($weight,$H);

            //$a->weight
			//Ед.
			$I = "I$endRow";
			$ed = $a->type?"шт":"кг";
			$this->setString($ed,$I);

			//Ставка.
			$J = "J$endRow";

			$rate = $a->rate;
			$this->setFloat($rate,$J);
			

			$K = "K$endRow";
			$this->setString('$',$K);

			//Сумма
			$L = "L$endRow";
			$s = $a->summa_us;
			//$s = sprintf("%.2f",$s);

			$this->setFloat($s,$L);


			$M = "M$endRow";
			$this->setString('$',$M);

			if($a->comment != ''){
				$this->setString($a->comment,"N$endRow");
			}
			$this->merge("N$endRow:P$endRow");

			$total += $a->summa_us;//$a->weight*$a->rate;

			if(!$a->type){
				$totalW += $a->weight; 
			}
			// $Lmerge = "L$endRow:M$endRow";
			// $this->objPHPExcel->getActiveSheet()->mergeCells($Lmerge);
		}

		if($a instanceof App){
			$autotruck = $a->autotruck;
			$course = $autotruck->course;
		}else{
			$course = 65.0539;
		}
		
		//$total = 2624.33;
		$totalNds = round($total/120*20,2);
		
		//$course = 65.0539;

		$block = "B$startRow:G$endRow";
		$this->objPHPExcel->getActiveSheet()->getStyle($block)->applyFromArray($this->styles['border-thick']);

		$startRow = $endRow +1;
		
		//Итого
		$Jmerge = "B$startRow:G$startRow";
		$this->merge($Jmerge);
		$td = "B$startRow";
		$this->setText('Итого',$td,1);
		$this->objPHPExcel->getActiveSheet()->getStyle("B$startRow:P$startRow")->applyFromArray($this->styles['border-thick']);

		$td = "H$startRow";
		$this->setText($totalW,$td,1);
		$td = "I$startRow";
		$this->setText("кг",$td,1);

		$td = "L$startRow";
		$this->setText($total,$td,1);
		$td = "M$startRow";
		$this->setText('$',$td,1);

	}


	function setPrice(){


	}


	public function export(Autotruck $autotruck){

		if(!$autotruck instanceof Autotruck) return null;

		$apps = $autotruck->getApps();
		$this->setHEad($autotruck);
		$this->setProducts($apps);
    
        $this->objPHPExcel->getActiveSheet()->setTitle('Заявка');


		$objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel5');
		$file_name = "Autotruck_".$autotruck->id."_".date("dmy",time()).".xls";
		$path = "files_xls/";
		$objWriter->save("{$path}{$file_name}");
		
		
            
        
		if (file_exists($path.$file_name)) {
    		return $path.$file_name;
		}

		return;
		
	}
}
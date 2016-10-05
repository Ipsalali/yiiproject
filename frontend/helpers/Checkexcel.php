<?php

namespace frontend\helpers;

use phpoffice\phpexcel\Classes\PHPExcel;
use frontend\models\App;
use common\models\Organisation;
use common\models\Client;
use Yii;

class Checkexcel{

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

	public function leadup_sheet(){
		if(!is_object($this->objPHPExcel)){
			return false;
		}

		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(3);

		$this->objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

		$this->objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(3);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(3);
	}

	public function setHeader($client){

		if(!is_object($this->objPHPExcel)){
			return false;
		}

		$org = Organisation::find()->where(['active'=>1])->one();
		if(!$org->id) return;
		//044525555 ПАО "ПРОМСВЯЗЬБАНК" Г. МОСКВА
		$objRichText = new \PHPExcel_RichText();
		$objRichText->createText($org->bank_name);
		$this->objPHPExcel->getActiveSheet()->getCell('B6')->setValue($objRichText);
		$this->objPHPExcel->getActiveSheet()->mergeCells('B6:H7');
		$this->objPHPExcel->getActiveSheet()->getStyle('B6:H7')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->objPHPExcel->getActiveSheet()->getStyle('B6:H8')->applyFromArray($this->styleArray);
		
		//Банк получателя
		$objRichText2 = new \PHPExcel_RichText();
		$objRichText2->createText('Банк получателя');
		$this->objPHPExcel->getActiveSheet()->getCell('B8')->setValue($objRichText2);
		$this->objPHPExcel->getActiveSheet()->mergeCells('B8:H8');


		//Инн
		$objRichText3 = new \PHPExcel_RichText();
		$objRichText3->createText('ИНН');
		$this->objPHPExcel->getActiveSheet()->getCell('B9')->setValue($objRichText3);
		$this->objPHPExcel->getActiveSheet()->mergeCells('B9:C9');

		//7725826870
		$objRichText = new \PHPExcel_RichText();
		$objRichText->createText($org->inn);
		$this->objPHPExcel->getActiveSheet()->getCell('D9')->setValue($objRichText);
		$this->objPHPExcel->getActiveSheet()->mergeCells('D9:E9');
		$this->objPHPExcel->getActiveSheet()->getStyle('B9:E9')->applyFromArray($this->styleArray);

		// //Инн
		$objRichText = new \PHPExcel_RichText();
		$objRichText->createText('КПП');
		$this->objPHPExcel->getActiveSheet()->getCell('F9')->setValue($objRichText);
		
		//774301001
		$objRichText = new \PHPExcel_RichText();
		$objRichText->createText($org->kpp);
		$this->objPHPExcel->getActiveSheet()->getCell('G9')->setValue($objRichText);
		$this->objPHPExcel->getActiveSheet()->mergeCells('G9:H9');
		$this->objPHPExcel->getActiveSheet()->getStyle('F9:H9')->applyFromArray($this->styleArray);


		//"ООО "ЗетаПро ""
		$objRichText = new \PHPExcel_RichText();
		$objRichText->createText($org->org_name);
		$this->objPHPExcel->getActiveSheet()->getCell('B10')->setValue($objRichText);
		$this->objPHPExcel->getActiveSheet()->mergeCells('B10:H11');
		$this->objPHPExcel->getActiveSheet()->getStyle('B10:H11')->applyFromArray($this->styleArray);

		//Получатель
		$objRichText2 = new \PHPExcel_RichText();
		$objRichText2->createText('Получатель');
		$this->objPHPExcel->getActiveSheet()->getCell('B12')->setValue($objRichText2);
		$this->objPHPExcel->getActiveSheet()->mergeCells('B12:H12');
		$this->objPHPExcel->getActiveSheet()->getStyle('B12:H12')->applyFromArray($this->styleArray);

		//БИК
		
		$objRichText = new \PHPExcel_RichText();
		$te = $objRichText->createTextRun('БИК');
		//$te->getFont()->setBold(true);
		$this->objPHPExcel->getActiveSheet()->getCell('I6')->setValue($objRichText);
		$this->objPHPExcel->getActiveSheet()->getStyle('I6')->applyFromArray($this->styleArray);
		$this->objPHPExcel->getActiveSheet()->getStyle('I6')->applyFromArray($this->font);

		//Сч. №
		$objRichText = new \PHPExcel_RichText();
		$objRichText->createText('Сч. №');
		$this->objPHPExcel->getActiveSheet()->getCell('I7')->setValue($objRichText);
		$this->objPHPExcel->getActiveSheet()->mergeCells('I7:I8');
		$this->objPHPExcel->getActiveSheet()->getStyle('I7:I8')->applyFromArray($this->styleArray);

		//Сч. №
		$objRichText = new \PHPExcel_RichText();
		$objRichText->createText('Сч. №');
		$this->objPHPExcel->getActiveSheet()->getCell('I9')->setValue($objRichText);
		$this->objPHPExcel->getActiveSheet()->mergeCells('I9:I12');
		$this->objPHPExcel->getActiveSheet()->getStyle('I9:I12')->applyFromArray($this->styleArray);


		//044525555 бик
		$objRichText = new \PHPExcel_RichText();
		$objRichText->createText($org->bik);
		$this->objPHPExcel->getActiveSheet()->getCell('J6')->setValue($objRichText);
		$this->objPHPExcel->getActiveSheet()->mergeCells('J6:M6');
		$this->objPHPExcel->getActiveSheet()->getStyle('J6:M6')->applyFromArray($this->styleArray);


		//30101810400000000555 счет номер 1
		$objRichText = new \PHPExcel_RichText();
		$objRichText->createText($org->bank_check);
		$this->objPHPExcel->getActiveSheet()->getCell('J7')->setValue($objRichText);
		$this->objPHPExcel->getActiveSheet()->mergeCells('J7:M8');
		$this->objPHPExcel->getActiveSheet()->getStyle('J7:M8')->applyFromArray($this->styleArray);

		//40702810400000028740  счет номер2
		$objRichText = new \PHPExcel_RichText();
		$objRichText->createText($org->org_check);
		$this->objPHPExcel->getActiveSheet()->getCell('J9')->setValue($objRichText);
		$this->objPHPExcel->getActiveSheet()->mergeCells('J9:M12');
		$this->objPHPExcel->getActiveSheet()->getStyle('J9:M12')->applyFromArray($this->styleArray);


		//Счет на оплату по договору № 57/15 от 23.07.2015
		$contract_check = 'Счет на оплату по договору № '.$client->contract_number;
		$this->setText($contract_check,'B14',1,0,15,0,1);
		$this->objPHPExcel->getActiveSheet()->getStyle('B14:M15')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$this->objPHPExcel->getActiveSheet()->mergeCells('B14:M15');
		
		//_________________________________________________
		$this->objPHPExcel->getActiveSheet()->mergeCells('B16:M16');
		$this->objPHPExcel->getActiveSheet()->getStyle('B16:M16')->applyFromArray($this->border_bottom);
		$this->objPHPExcel->getActiveSheet()->getRowDimension(16)->setRowHeight(12);

		
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
			//$style->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		}
		if($wrap){
			//print_r($style->getAlignment());
			// $style->getAlignment()->setWrapText(true);
		}
		


		$this->objPHPExcel->getActiveSheet()->getCell($coords)->setValue($titleName);

	}

	public function merge($coords){
		$this->objPHPExcel->getActiveSheet()->mergeCells($coords);
	}

	public function setSeller($seller=""){

		$org = Organisation::find()->where(['active'=>1])->one();
		if(!$org->id) return;
		$objPHPExcel = $this->objPHPExcel;
		//Поставщик

		$objRichText = new \PHPExcel_RichText();
		$objRichText->createText('Поставщик:');
		$this->objPHPExcel->getActiveSheet()->getCell('B18')->setValue($objRichText);
		$this->objPHPExcel->getActiveSheet()->mergeCells('B18:C18');
		



		$seller = $org->org_name.", ИНН ".$org->inn.", КПП ".$org->kpp.' , '.$org->org_address;
		$this->setText($seller,'D18',1);
		$objPHPExcel->getActiveSheet()->getStyle('D18')->getAlignment()->setWrapText(true);
		$this->objPHPExcel->getActiveSheet()->mergeCells('D18:M18');
		$this->objPHPExcel->getActiveSheet()->getRowDimension(18)->setRowHeight(30);
	}


	public function setBuyer($client){
		//Покупатель
		$objRichText = new \PHPExcel_RichText();
		$objRichText->createText('Покупатель:');
		$this->objPHPExcel->getActiveSheet()->getCell('B20')->setValue($objRichText);
		$this->objPHPExcel->getActiveSheet()->mergeCells('B20:C20');


		$buyer = $client->full_name? $client->full_name:"Не указан";
		$this->setText($buyer,'D20',1);
		$this->objPHPExcel->getActiveSheet()->mergeCells('D20:M20');
		$this->objPHPExcel->getActiveSheet()->getStyle('D20:M20')->applyFromArray($this->styles['border-thick']);
	}

	public function setProducts($apps,$client){
		if(!is_array($apps) || !count($apps)) return false;

		$startRow = 22;
		$endRow = $startRow;
		
		$this->setText("№","B22",1);

		$this->setText("Артикул","C22",1);

		$this->setText("Товары (работы, услуги)","D22",1);

		$this->objPHPExcel->getActiveSheet()->mergeCells('D22:G22');
		$this->setText("Кол-во","H22",1);

		$this->setText("Ед.","I22",1);

		$this->setText("Ставка","J22",1);

		$this->objPHPExcel->getActiveSheet()->mergeCells('J22:K22');
		$this->setText("Сумма","L22",1);

		$this->objPHPExcel->getActiveSheet()->mergeCells('L22:M22');

		foreach ($apps as $key => $a) {
			$endRow++;

			$B="B$endRow";
			$this->setText($key+1,$B);

			//Артикул пропускаем

			//Наименование
			$D="D$endRow";
			$this->setText($a->info,$D);
			$Dmerge = "D$endRow:G$endRow";
			$this->objPHPExcel->getActiveSheet()->mergeCells($Dmerge);

			//Кол-во
			$H = "H$endRow";
			$this->setText($a->weight,$H);

			//Ед.
			$I = "I$endRow";
			$ed = $a->type?"шт":"кг";
			$this->setText($ed,$I);

			//Ставка.
			$J = "J$endRow";
			$this->setText($a->rate,$J);

			$K = "K$endRow";
			$this->setText('$',$K);
			// $Jmerge = "J$endRow:K$endRow";
			// $this->objPHPExcel->getActiveSheet()->mergeCells($Jmerge);

			//Сумма
			$L = "L$endRow";
			$this->setText($a->weight*$a->rate,$L);

			$M = "M$endRow";
			$this->setText('$',$M);

			$total += $a->weight*$a->rate; 
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
		$totalNds = round($total/118*18,2);
		
		//$course = 65.0539;

		$block = "B$startRow:M$endRow";
		$this->objPHPExcel->getActiveSheet()->getStyle($block)->applyFromArray($this->styles['border-thick']);

		$startRow = $endRow +2;
		//Итого
		$Jmerge = "J$startRow:K$startRow";
		$this->objPHPExcel->getActiveSheet()->mergeCells($Jmerge);
		$td = "J$startRow";
		$this->setText('Итого',$td,1);
		$this->objPHPExcel->getActiveSheet()->getStyle($Jmerge)->applyFromArray($this->styles['font_mini']);

		$td = "L$startRow";
		$this->setText($total,$td,1);
		$td = "M$startRow";
		$this->setText('$',$td,1);

		$startRow +=1; 
		//В том числе НДС
		$Jmerge = "J$startRow:K$startRow";
		$this->objPHPExcel->getActiveSheet()->mergeCells($Jmerge);
		$this->objPHPExcel->getActiveSheet()->getStyle($Jmerge)->applyFromArray($this->styles['font_mini']);
		$td = "J$startRow";
		$this->setText('В том числе НДС',$td,1);

		$td = "L$startRow";
		$this->setText($totalNds,$td,1);
		$td = "M$startRow";
		$this->setText('$',$td,1);

		$startRow +=1;
		//Всего к оплате
		$Jmerge = "J$startRow:K$startRow";
		$this->objPHPExcel->getActiveSheet()->mergeCells($Jmerge);
		$this->objPHPExcel->getActiveSheet()->getStyle($Jmerge)->applyFromArray($this->styles['font_mini']);		
		$td = "J$startRow";
		$this->setText('Всего к оплате',$td,1);

		$td = "L$startRow";
		$this->setText($total,$td,1);
		$td = "M$startRow";
		$this->setText('$',$td,1);

		$startRow +=1;

		// $merge = "B$startRow:M$startRow";
		// $this->merge($merge);
		// $str = "Всего наименований ".count($apps).", на сумму 2 595,30 USD";
		// $this->setText($str,"B$startRow");

		// $startRow +=1;
		// $merge = "B$startRow:M$startRow";
		// $this->merge($merge);
		// $str = "Две тысячи пятьсот девяносто пять долларов 30 центов";
		// $this->setText($str,"B$startRow");

		$blue = \PHPExcel_Style_Color::COLOR_BLUE;

		$total = $total*$course;
		$startRow +=2;
		$merge = "B$startRow:M$startRow";
		$this->merge($merge);
		$str = "Оплата по курсу ЦБ на день оплаты, но не ниже: $".$course;
		$this->setText($str,"B$startRow",1,$blue);

		$startRow +=2;
		$merge = "B$startRow:I$startRow";
		$this->merge($merge);
		$str = "Итого к оплате в рублях";
		$this->setText($str,"B$startRow",1,$blue);

		if($client->payment_clearing){
			$beznal = round($total + ($total*$client->payment_clearing/100),2);
			$bexnalNds = round($beznal/118*18,2);	
		}else{
			$beznal =  $total;
			$bexnalNds = round($total/118*18,2);
		}

		$totalNds = round($total/118*18,2);

		$startRow +=2;
		$merge = "B$startRow:E$startRow";
		$this->merge($merge);
		$str = "Оплата б/н (+".$client->payment_clearing."%):";
		$this->setText($str,"B$startRow",1,$blue);

		$str = $beznal;
		$this->setText($str,"F$startRow",1,$blue);
		$this->setText("р","G$startRow",1,$blue);

		$startRow +=1;
		$merge = "B$startRow:E$startRow";
		$this->merge($merge);
		$str = "В том числе НДС:";
		$this->setText($str,"B$startRow",1,$blue);

		$str =  $bexnalNds;
		$this->setText($str,"F$startRow",1,$blue);
		$this->setText("р","G$startRow",1,$blue);


		$startRow +=2;
		$merge = "B$startRow:E$startRow";
		$this->merge($merge);
		$str = "Оплата наличными:";
		$this->setText($str,"B$startRow",1,$blue);

		
		$str =  round($total,2);
		$this->setText($str,"F$startRow",1,$blue);
		$this->setText("р","G$startRow",1,$blue);
		

		$startRow +=1;
		$merge = "B$startRow:E$startRow";
		$this->merge($merge);
		$str = "В том числе НДС:";
		$this->setText($str,"B$startRow",1,$blue);

		$str =  $totalNds;
		$this->setText($str,"F$startRow",1,$blue);
		$this->setText("р","G$startRow",1,$blue);

		$startRow +=2;
		$td = "B$startRow:M$startRow";
		$this->merge($td);
		$this->objPHPExcel->getActiveSheet()->getStyle($td)->applyFromArray($this->border_bottom);

		$org = Organisation::find()->where(['active'=>1])->one();
		$headman = $org->id ? $org->headman : "";
		$startRow +=2;
		$this->setText("Руководитель","B$startRow",1);
		$this->merge("B$startRow:D$startRow");

		$this->setText($headman,"E$startRow");
		$this->merge("E$startRow:G$startRow");
		$td = "E$startRow:G$startRow";
		$this->objPHPExcel->getActiveSheet()->getStyle($td)->applyFromArray($this->border_bottom);

		$this->setText("Бухгалтер","I$startRow",1);
		$this->merge("I$startRow:J$startRow");

		$this->merge("K$startRow:M$startRow");
		$td = "K$startRow:M$startRow";
		$this->objPHPExcel->getActiveSheet()->getStyle($td)->applyFromArray($this->border_bottom);
	}


	function setPrice(){


	}


	public function generateCheck($apps,$client){

		$this->leadup_sheet();
		$this->setHeader($client);
		$this->setSeller();
		$this->setBuyer($client);
		$this->setProducts($apps,$client);

        $this->objPHPExcel->getActiveSheet()->setTitle('Simple');

        //$objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
		//$objWriter->save("files_xls/check.xlsx");

		$objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel5');
		$file_name = "check_".$client->id."_".date("dmy",time()).".xls";
		$path = "files_xls/";
		$objWriter->save("{$path}{$file_name}");
		

		if (file_exists($path.$file_name)) {
    		return $path.$file_name;
		}

		return;
		
	}
}
<?php 

$html = "";
if(is_array($apps) && count($apps)){
	$html = "<ul>";
	foreach ($apps as $key => $a) {
		$html .="<li>";
		$html .=  $a->description." (".$a->weight." кг.)";
		$html .="</li>";
		
	}

	$html .="</ul>";
}
$trace_date = date("d.m.Y",strtotime($activeTrace->trace_date));
$country = $autotruck_model->countryName;
$course = $autotruck_model->course." руб.";
$date = date("d.m.Y",strtotime($autotruck_model->date));
$gtd = $autotruck_model->gtd;

$msg = $activeStatus->notification_template;
$msg = str_replace('[APP_LIST]', $html, $msg); //вставляем список наименовании
$msg = str_replace('[APP_STATUS]', $activeStatus->title, $msg); // вставляем активный статус
$msg = str_replace('[APP_STATUS_DATE]', $trace_date, $msg);//вставляем дату активного статуса
$msg = str_replace('[APP_COUNTRY]', $country, $msg); // вставляем страну поставки
$msg = str_replace('[APP_COURSE]', $course, $msg); // вставляем курс
$msg = str_replace('[APP_DATE]', $date, $msg); // вставляем курс
$msg = str_replace('[APP_GTD]', $gtd, $msg); // вставляем курс
?>

<?php echo $msg;?>

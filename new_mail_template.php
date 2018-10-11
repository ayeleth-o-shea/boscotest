<?php
$_SERVER["DOCUMENT_ROOT"] = (!$_SERVER["DOCUMENT_ROOT"]) ? "/home/bitrix/www" : $_SERVER["DOCUMENT_ROOT"];
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); 


$em = new CEventMessage;
$fields = array(
			"ACTIVE"		    => 'Y',
			"EVENT_NAME"	    => 'USER_INFO', //for example only
			"LID"			    => 's1',
			"EMAIL_FROM"	    => '#DEFAULT_EMAIL_FROM#',
			"EMAIL_TO"		    => "#EMAIL#",
			"BCC"			    => '#BCC#'
			"SUBJECT"		    => "Шаблон Boscotest",
			"MESSAGE"		    => "Привет!",
			"BODY_TYPE"		    => "text"
);

$ID = $em->Add($arFields);
echo "Добавлен новый шаблон: $ID";
	
require($_SERVER["DOCUMENT_ROOT"].
"/bitrix/modules/main/include/epilog_after.php");
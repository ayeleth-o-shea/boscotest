<?php
/**
 * Created by PhpStorm.
 * User: ayeleth
 * Date: 11.10.18
 * Time: 15:04
 */
define("NEED_AUTH", false);
define('NO_KEEP_STATISTIC', true); 
define('NOT_CHECK_PERMISSIONS',true); 
define('BX_NO_ACCELERATOR_RESET', true);

set_time_limit(0); 
 
$_SERVER["DOCUMENT_ROOT"] = (!$_SERVER["DOCUMENT_ROOT"]) ? "/home/bitrix/www" : $_SERVER["DOCUMENT_ROOT"];
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); 

//для phpoffice/spreadsheet
//сейчас не нужен - подключен в php_interface/init.php
//require_once($_SERVER["DOCUMENT_ROOT"] .'/vendor/autoload.php');

$controller = new \Boscotest\Controller([
				new XlsHelper([
					'source' => 'clients_list.xlsx',
					'limit' => 10,
					'startRow' => 2
				])
			]);

$controller->process();

require($_SERVER["DOCUMENT_ROOT"].
"/bitrix/modules/main/include/epilog_after.php");

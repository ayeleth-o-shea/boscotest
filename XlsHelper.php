<?php

namespace Boscotest;

use \PhpOffice\PhpSpreadsheet\Reader\Xlsx,
	\PhpOffice\PhpSpreadsheet\IOFactory;
	
/**
 * класс для работы с xlsx-файлами, использует сторонню библиотеку для работы с файлами office/xls
 *
 * Class XlsHelper
 *
 * @package Boscotest
 */

class XlsHelper
{
	/*
	* @var array $contacts - контакты
	*/
	private $contacts = [];
	
	/*
	* @var string $source - путь к файлу-источнику
	*/
	private $source = '';
	
	/*
	* @var int $limit - ограничение на количество строк
	*/
	private $limit = '';
	
	/*
	* @var int $startRow - начальная строка
	*/
	private $startRow = 2;
	
	/*
	* @var PhpOffice\PhpSpreadsheet\Reader\Xlsx $xlsObj - объект Xlsx
	*/
	private $xlsObj;
	
	/*
	* @var array COLUMN_NAMES - соответствие полей контакта и колонок документа
	*/
	const COLUMN_NAMES = [
		 'A' => 'LAST_NAME',
		 'B' => 'NAME',
		 'C' => 'SECOND_NAME',
		 'D' => 'COMPANY_TITLE',
		 'E' => 'BIRTHDATE'
		 ];
	

	function __construct($params)
	{
		$this->source = $params['source'];
		$this->limit = $params['limit'];
		$this->startRow = $params['startRow'];
		$this->xlsObj = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		$this->loadData();
		$this->setContacts();
	}
	
	
	/*
	* загрузка данных из файла
	* @return mixed
	*/
	private function loadData()
	{
		try{
			$this->xlsObj->setReadDataOnly(true);
			$spreadsheet = $this->xlsObj->load($this->source);
			return $spreadsheet->getActiveSheet()->getCellCollection();
		}
		catch (\Exception $e) {
            Logger::getInstance('test')->warning('Что-то пошло не так при загрузке данных из экселя');
            return false;
        }
	}
	
	
	/*
	* записываем найденные контакты в свойство contacts
	* @return mixed
	*/
	private function setContacts()
	{
		$data = $this->loadData($this->xlsObj);
		for ($row = $this->startRow; $row <= $cells->getHighestRow(); $row++){
			$ar = [];
			for ($col = 'A'; $col <= 'E'; $col++) {
				$value = $cells->get($col.$row)->getValue();
				$ar[$keys[$col]] = $value;
			}
			$this->contacts[] = $ar;
		}
	}
	
	
	/*
	* получаем найденные контакты
	* @return mixed
	*/
	public function getContacts()
	{
		return $this->contacts;
	}
	
}

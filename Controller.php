<?php
namespace Boscotest;

use Bitrix\Main\Loader,
	Logger;

Loader::includeModule("crm");

/**
 * класс-контроллер, загружает контакты и определяет, что именно с ними делать
 *
 * Class Controller
 *
 * @package Boscotest
 */

class Controller
{
	private $existingContacts = [];
	private $newContacts = [];
	private $xlsObj = null;
	
	const MAIL_TEMPLATE_ID = 100;
	
	function __construct(XlsHelper $xlsObj)
	{
		if (!is_null($import))
		{
			$this->xlsObj = $xlsObj;
			$contacts = $this->xlsObj->getContacts();
			foreach ($contacts as $c)
			{
				if(CrmHelper::exists('contact', $c))
					$this->$existingContacts[] = $c;
				else $this->$newContacts[] = $c;
			}
		}
		else throw new \Exception("Нужен объект XlsHelper!");
	}
	
	/*
	* запись в crm несуществующих контактов
	* @return mixed
	*/
	public function import()
	{
		try{
			
			foreach ($this->newContacts as $contact)
			{
				$ct = new \CCrmContact(false);
				$params = ['HAS_PHONE'=>'N'];
				  
				$params['FULL_NAME'] = $contact['LAST_NAME']." ".$contact['NAME']." ".$contact['SECOND_NAME'];
				$params['LAST_NAME'] = $contact['LAST_NAME'];
				$params['NAME'] = $contact['NAME'];
				$params['HAS_EMAIL']='N';
				$params['HAS_PHONE']='N';
				$params['TYPE_ID'] ='CLIENT';
				$params['SOURCE_ID']= 'WEB';
				$params['OPENED'] = 'Y';


				$contactId =$ct->Add[$params, true, ['DISABLE_USER_FIELD_CHECK' => true]];

				if ($contactId){
					//TODO @Ayeleth: проверить на существование компанию, если нет то добавить и привязать к пользователю
					//	$company = $contact['COMPANY_TITLE'];
					//  if (!CrmHelper::exists('company', $company))
					//  {
					//		добавление, привязка	
					//	}
					//	
					return $contactId;
				}
				else{
					Logger::getInstance('test')->warning('Контакт crm при импорте не добавлен:' . $ct->LAST_ERROR, [$ct->LAST_ERROR])
					return false;
				}				
					
			}
		}
		catch (\Exception $e) {
            Logger::getInstance('test')->warning('Что-то пошло не так при импорте');
            return false;
        }

	}
	
	/*
	* отсылка на почту существующих контактов
	* @return boolean
	*/
	private function sendMail()
	{
        try {
			
			$contactsList = '';
			
			if (count($this->existingContacts) > 0)
				$contactsList = "Сегодня день рожденья у:\n";
			
			foreach ($this->existingContacts as $contact)
			{
				if ($this->datesEqual($contact['BIRTHDATE'], date('d.m.Y')))
				{
					$contactsList .= "{$contact['LAST_NAME'] $contact['NAME'] $contact['SECOND_NAME'] $contact['COMPANY_TITLE']}\n";
				}
				
				
			}
			
			if (strlen($contactsList) > 0)
			{
				 \Bitrix\Main\Mail\Event::sendImmediate([
					"EVENT_NAME" => "ANY_TEMPLATE",
					"LID" => "sb",
					"MESSAGE_ID" => self::MAIL_TEMPLATE_ID,
					"C_FIELDS" => [
						"CONTACTS_LIST" => $contactsList
					],
				]);
			}
			else 
			{
				Logger::getInstance('test')->info('Сегодня ни у кого из контактов нет дня рождения');
			}
			return true;	
           
        } catch (\Exception $e) {
            Logger::getInstance('test')->warning('Что-то пошло не так при отсылке писем');
            return false;
        }
	}
	
	/*
	* сравнение дат
	* @param string $date1
	* @param string $date2
	* @return boolean
	*/
	private function datesEqual($date1, $date2)
	{
		$result=(strtotime($date1)==strtotime($date2)); 
		return $result;
	}
	
	/*
	* отсылает либо добавляет контакты
	*/
	function process()
	{
		
		if (count($this->existingContacts) > 0)
			$this->sendMail();
		if (count($this->existingContacts) > 0)
			$this->import();
	}
	
}
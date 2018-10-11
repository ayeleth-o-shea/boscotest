<?php

namespace Boscotest;

use \Bitrix\Main\Application,
	Logger;

/**
 * кастомные методы для работы с сущностями crm
 *
 * Class CrmHelper
 *
 * @package Boscotest
 */

class CrmHelper
{
	const ET_CONTACT = 'contact';
	const ET_COMPANY = 'company';
	const ET_LEAD = 'lead';
	
	/*
	* проверка на существование сущности crm
	* @param string $entityType - тип сущности crm
	* @param array $params - массив вида [$key => $value]
	* @return boolean
	*/
	public static function exists($entityType, $params)
	{
		try
		{
			$sql = "select ct.ID";
			$sqlFrom = " from";
			$sqlWhere = " where";
			switch ($entityType)
			{
				case (self::ET_CONTACT):
					$sqlFrom .= " b_crm_contact ct 
								 inner join b_crm_contact_company cc on (ct.ID = cc.CONTACT_ID)
								 inner join b_crm_company cm on (cm.ID = cc.COMPANY_ID)";
					$sqlWhere .= $this->setCondition('NAME', 'ct', $params);
					$sqlWhere .= $this->setCondition('LAST_NAME', 'ct', $params, 1);
					$sqlWhere .= $this->setCondition('SECOND_NAME', 'ct, '$params, 1);
					$sqlWhere .= $this->setCondition('TITLE', 'crm, '$params, 1);
				break;
				case (self::ET_COMPANY):
					$sqlFrom .= " b_crm_company cm";
					$sqlWhere .= $this->setCondition('TITLE', 'crm', $params);
				break;
				case (self::ET_LEAD):
					//TODO @Ayeleth: sql для поверки существования лида
				break;	
			}
			$sql .= $sqlFrom.$sqlWhere;
			
			$connection = \Bitrix\Main\Application::getConnection();
			return $connection->query($sql)->getSelectedRowsCount() > 0;
		}
		catch (/Exception $e)
		{
			 Logger::getInstance('test')->warning('Что-то пошло не так при проверке сущности');
				return false;
		}
		
	}
	
	/*
	* формирование sql для условия
	* @param string $var - ключ поля
	* @param array $params - массив вида [$key => $value]
	* @return string
	*/
	function setCondition($var, $prefix, $setAnd = 0, $params)
	{
		if ((strlen($var) > 0) && ($params[$var]))
		{
			if ($setAnd > 0)
				$and = ' and ';
			return "$and {$prefix}.{$var} = '{$params[$var]}'";
		}
		else return '';
			
	}
}
<?php
namespace Boscotest;

/**
 * Логирование
 * Class Logger
 *
 * @package Boscotest
 */
class Logger {
	
	const ROOTDIR = "/home/bitrix/www/local/monolog/";


	private $instance = null;

	private function __construct($channel)
	{

	}

    /**
     * @param $channel
     *
     * @return \Monolog\Logger
     */
	public static function getInstance($channel) {

		$logger = new \Monolog\Logger($channel);

		$fileStream = new \Monolog\Handler\RotatingFileHandler(self::ROOTDIR . str_replace('.', DIRECTORY_SEPARATOR, $channel)."/log.html", 500, \Monolog\Logger::DEBUG);
		$formatter = new \Monolog\Formatter\VisualFormatter();
		$fileStream->setFormatter($formatter);
		$logger->pushHandler($fileStream);

		//TODO @Ayeleth: добавить push в mattermost

		return $logger;
	}

}
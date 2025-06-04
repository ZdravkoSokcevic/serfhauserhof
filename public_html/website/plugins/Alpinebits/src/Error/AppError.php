<?php
namespace Alpinebits\Error;

use Cake\Routing\Exception\MissingControllerException;
use Cake\Routing\Exception\MissingViewException;
use Cake\Routing\Exception\MissingActionException;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Error\ErrorHandler;

class AppError extends ErrorHandler {

	public function _displayException($exception)
    {
		header('Content-type: application/xml');
		$content = '<?xml version="1.0" encoding="UTF-8"?>';
		$content .= '<OTA_ResRetrieveRS xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.opentravel.org/OTA/2003/05" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 OTA_ResRetrieveRS.xsd" Version="7.000">' . "\n";
		$content .= '<Errors>';	
			$content .= '<Error Type="13" Code="448">';
				$content .= 'Message: "' . $exception->getMessage() . '"' . "\n";	
				$content .= 'in the file: "' . $exception->getFile() . '"' . "\n";	
				$content .= 'at line: "' . $exception->getLine() . '"' . "\n";		
				$content .= 'with code: "' . $exception->getCode() . '"' . "\n";
				$content .= 'Trace: "' . htmlspecialchars($exception->getTraceAsString()) . '"'; //this causes errors	
			$content .= '</Error>';
		$content .= '</Errors>';
		$content .= '</OTA_ResRetrieveRS>';	
		
		die($content);
    }
    
}

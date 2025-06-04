<?php
namespace Frontend\Error;

use Cake\Error\ErrorHandler;

class AppError extends ErrorHandler {
    
    public function _displayError($error, $debug) {
        parent::_displayError($error, $debug);
    }

    public function _displayException($exception) {
        parent::_displayException($exception);
    }

}

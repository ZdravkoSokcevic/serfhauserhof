<?php
namespace Backend\Error;

use Cake\Routing\Exception\MissingControllerException;
use Cake\Routing\Exception\MissingViewException;
use Cake\Routing\Exception\MissingActionException;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Error\ErrorHandler;

class AppError extends ErrorHandler {
    public function _displayException($exception) {
        if ($exception instanceof MissingControllerException) {
            header('Location: /admin/');
            exit;
        } else if ($exception instanceof MissingViewException) {
            header('Location: /admin/');
            exit;
        } else if ($exception instanceof MissingActionException) {
            header('Location: /admin/');
            exit;
        } else if ($exception instanceof MissingRouteException) {
            header('Location: /admin/');
            exit;
        } else {
            parent::_displayException($exception);
        }
    }

}

<?php

namespace app\controllers\api;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\web\Response;

/**
 * Main Api Controller
 * 
 */
class ApiController extends \yii\rest\Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'registration' => ['post'],
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actions() {
        $actions = parent::actions();
        /* $actions['creategroup'] = [
          'class' => 'app\controllers\api\GroupController',
          ]; */
        return $actions;
    }

    protected function setHeader($status) {
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        $content_type = "application/json; charset=utf-8";

        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . "Nintriva <nintriva.com>");
    }

    /*
     * Response status code
     */
    protected function _getStatusCodeMessage($status) {
        $codes = Array(
            200 => 'OK',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    /**
     * response method with custom structure
     * @param integer $code - header status code
     * @param integer $statusCode - response server status
     * @param mixed $data - response data
     * @param mixed $message - response comment
     * @return JSON
     */
    protected function sendResponse($code, $statusCode = null, $data = null, $message = null) {
        if (is_null($message) || $message == '') {
            $message = $this->_getStatusCodeMessage($code);
        }

        $this->setHeader($code);
        echo json_encode(array('message' => $message, 'status' => $statusCode, 'data' => $data), JSON_PRETTY_PRINT);
        exit;
    }

}

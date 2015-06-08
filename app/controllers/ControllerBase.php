<?php

use Phalcon\Mvc\Controller;

$base_is_loaded = false;

/**
 * @property mixed assets
 */
class ControllerBase extends Controller
{
    /**
     * @var User
     */
    protected $logged_user;

    /**
     * @var Phalcon\Db\Adapter
     */
    public $db;

    public function initialize() {

        return true;
    }

    /**
     * @return bool
     */
    public function isLogged() {
        $user_id = $this->session->get("user_id");
        if($user_id) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getLoggeduser() {
        return $this->logged_user;
    }

    public function setJsonContent($status, $message = "", $data = null) {

        $content = new stdClass();
        $content->status = $status;
        $content->message = $message;
        $content->data = $data;

        $this->response->setJsonContent($content);
        $this->response->send();
        $this->view->disable();
        return true;
    }

    /**
     * @param \Phalcon\Mvc\Model  $model
     * @return bool
     */
    public function setJsonModel($model) {

        $messages = $model->getMessages();
        if(count($messages)>0) {
            foreach($messages as $message) {
                return $this->setJsonContent("error", $message->getMessage(), $message->getField());
            }
        }
        return $this->setJsonContent("error", "알 수 없는 오류");
    }

}


function cp(){

    $args = func_get_args();

    foreach($args as $val){
        if(gettype($val) == 'array'){
            error_log("\n".print_r($val, true));
        }
        else if(gettype($val) == 'boolean'){
            if($val == 1){
                error_log("\ntrue");
            }
            else{
                error_log("\nfalse");
            }
        }
        else if(gettype($val) == 'integer'){
            error_log("\n".$val);
        }
        else if(gettype($val) == 'double'){
            error_log("\n".$val);
        }
        else if(gettype($val) == 'string'){
            error_log("\n".$val);
        }
        else if(gettype($val) == 'NULL'){
            error_log("\nNULL\n");
        }
        else if(gettype($val) == 'unknown type'){
            error_log("\nunknown type\n");
        }
        else if(gettype($val) == 'resource'){
            error_log("\nresource\n");
        }
        else if(gettype($val) == 'object'){
            error_log("\nobject\n");
        }
    }

}

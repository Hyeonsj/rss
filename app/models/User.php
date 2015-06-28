<?php

use Phalcon\Mvc\Model\Validator\Email,
    Phalcon\Mvc\Model\Validator\InclusionIn,
    Phalcon\Mvc\Model\Validator\StringLength,
    Phalcon\Mvc\Model\Validator\Uniqueness,
    Phalcon\Mvc\Model\Resultset\Simple as Resultset;


class User extends ModelBase
{
    public $user_id;
    public $email;
    public $password;
    public $nickname;
    public $new_password;
    public $new_password_created_at;
    public $is_admin = 0;
    public $email_cert_code;
    public $login_count;
    public $last_login_date;
    public $created_at;
    public $updated_at;


    /**
     * Validations and business logic
     */
    public function validation()
    {

        $this->validate(
            new Email(
                array(
                    'field'    => 'email',
                    'required' => false,
                )
            )
        );

        $this->validate(
            new Uniqueness(
                array(
                    'field'     => 'email',
                    'message'   => 'Email address already in use.'
                )
            )
        );

        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }

    public function afterFetch() {

    }

    public function getInfo() {
        $info = new stdClass();
        $info->user_id = $this->user_id;
        $info->nickname = $this->nickname;
        $info->email = $this->email;
        $info->created_at = $this->created_at;
        $info->updated_at = $this->updated_at;

        return $info;
    }

    public function save($data = null, $whiteList = null)
    {
        if(!$this->user_id) {
            $this->created_at = time();
        }
        $this->updated_at = time();
        return parent::save($data, $whiteList);
    }


    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model\ResultsetInterface|User[]
     */
    public static function find($parameters=null) {
        return parent::find($parameters);
    }

    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model|User
     */
    public static function findFirst($parameters=null) {
        return parent::findFirst($parameters);
    }


}

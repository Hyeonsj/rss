<?php

class UserMenu extends ModelBase {

    public $user_menu_id;
    public $user_id;
    public $title;
    public $link;
    public $view_type;
    public $category_type;
    public $view_count;
    public $readed_at;
    public $created_at;
    public $updated_at;

    public function save($data = null, $whiteList = null)
    {
        if(!$this->user_menu_id) {
            $this->created_at = time();
        }
        $this->updated_at = time();
        $this->readed_at = time();
        return parent::save($data, $whiteList);
    }

    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model\ResultsetInterface|Attend[]
     */
    public static function find($parameters=null) {
        return parent::find($parameters);
    }

    /**sour
     * @param null $parameters
     * @return \Phalcon\Mvc\Model|Attend
     */
    public static function findFirst($parameters=null) {
        return parent::findFirst($parameters);
    }

}
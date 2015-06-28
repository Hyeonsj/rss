<?php

class Site extends ModelBase {

    public $site_id;
    public $url;
    public $count;
    public $time;
    public $created_at;
    public $updated_at;

    public function save($data = null, $whiteList = null)
    {
        if(!$this->site_id) {
            $this->created_at = time();
        }
        $this->updated_at = time();
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
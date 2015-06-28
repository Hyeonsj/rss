<?php
class Context {
    public static $arr = array();
    public static $body_class = array();
    public static $header = '';
    public static function set($key, $val) {
        Context::$arr[$key] = $val;
    }
    /**
     * @param $key
     * @return null|string
     */
    public static function get($key) {
        if(isset(Context::$arr[$key])) {
            return Context::$arr[$key];
        }
        return null;
    }
    /**
     * body tag 에 클래스를 추가한다.
     * @param string $class_name
     */
    public static function addBodyClass($class_name) {
        if(!in_array($class_name, Context::$body_class)) {
            Context::$body_class[] = $class_name;
        }
    }
    /**
     * body tag에 클래스를 제거한다.
     * @param string $class_name
     */
    public static function removeBodyClass($class_name) {
        if(in_array($class_name, Context::$body_class)) {
            foreach(Context::$body_class as $key=>$val) {
                if($val==$class_name) {
                    unset(Context::$body_class[$key]);
                    break;
                }
            }
        }
    }
    public static function addHeader($html) {
        Context::$header .= $html;
    }
    public static function getHeader() {
        return Context::$header;
    }
    /**
     * @return array
     */
    public static function getBodyClasses() {
        return Context::$body_class;
    }
    public static function isDevelopmentMode() {
        return true;
    }
    public static function isProductionMode() {
        return !Context::isDevelopmentMode();
    }
    public static function getPostypeUrl() {
        return POSTYPE_URL;
    }
    public static function getPostypeDomain() {
        return POSTYPE_DOMAIN;
    }
    public static function getPostypeProtocol() {
        return POSTYPE_PROTOCOL;
    }
}
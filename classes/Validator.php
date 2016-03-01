<?php
/**
 * Created by PhpStorm.
 * User: Melle Dijkstra
 * Date: 8-5-2015
 * Time: 11:55
 */

class Validator {

    public static function name($data,$min = null,$max = null) {
        $regex = "/^([a-zA-Z0-9 ]*[a-zA-Z ]+[a-zA-Z0-9 ]*)$/";
        if(preg_match($regex, $data)) {
            if(is_numeric($min) && is_numeric($max)) {
                return strlen($data) >= $min && strlen($data) <= $max ? true : false;
            } else {
                if(is_numeric($min) && !is_numeric($max)) {
                    return strlen($data) >= $min ? true : false;
                } elseif(!is_numeric($min) && is_numeric($max)) {
                    return strlen($data) <= $max ? true : false;
                } else {
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    public static function email($data) {
        return filter_var($data, FILTER_VALIDATE_EMAIL) ? true : false;
    }

    public static function date($data) {
        $regex = '/^(19[0-9]{2}|2[0-9]{3})-(0[1-9]|1[012])-([123]0|[012][1-9]|31)$/';
        return preg_match($regex, $data) ? true : false;
    }

    public static function number($data,$minlength = null,$maxlength = null) {
        $regex = '/^[0-9]+$/';
        $length = strlen($data);
        return preg_match($regex, $data) ? (is_numeric($minlength) && is_numeric($maxlength) ? ($length >= $minlength && $length <= $maxlength ? true : false) : true) : false;
    }

    public static function image($data) {
        $regex = '/([^\s]+(\.(jpg|png|gif|bmp))$)/';
        return preg_match($regex, $data) ? true : false;
    }

    public static function password($data) {
        $regex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/';
        return preg_match($regex, $data) ? true : false;
    }

    public static function url($data) {
        return !filter_var($data, FILTER_VALIDATE_URL) === false ? true : false;
    }

    public static function gender($data) {
        return (in_array($data,array('m','f')));
    }

    public static function nickname($data) {
        $regex = '/^[A-Za-z]+$/';
        return preg_match($regex,$data) ? true : false;
    }

    public static function color($color, $type) {
        switch($type) {
            case 'rgb':
                $regex = '/^rgb\\(\\s*(0|[1-9]\\d?|1\\d\\d?|2[0-4]\\d|25[0-5])\\s*,\\s*(0|[1-9]\\d?|1\\d\\d?|2[0-4]\\d|25[0-5])\\s*,\\s*(0|[1-9]\\d?|1\\d\\d?|2[0-4]\\d|25[0-5])\\s*\\)$/';
                return preg_match($regex,$color) ? true : false;
                break;
            case 'hex':
                $regex = '/^#[0-9a-f]{3}([0-9a-f]{3})?$/';
                return preg_match($regex,$color) ? true : false;
                break;
            case 'rgba':
                $regex = '/^rgba\\(\\s*(0|[1-9]\\d?|1\\d\\d?|2[0-4]\\d|25[0-5])\\s*,\\s*(0|[1-9]\\d?|1\\d\\d?|2[0-4]\\d|25[0-5])\\s*,\\s*(0|[1-9]\\d?|1\\d\\d?|2[0-4]\\d|25[0-5])\\s*,\\s*((0.[1-9])|[01])\\s*\\)$/';
                return preg_match($regex,$color) ? true : false;
                break;
        }
        return false;
    }

}
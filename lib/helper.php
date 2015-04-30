<?php

class helper {
    /*
     * 获取当前的时间戳（精确到毫秒）
     */

    public static function float_time() {
        list($usec, $sec) = explode(' ', microtime());
        return (float) $usec + (float) $sec;
    }

    /*
     * 获取IP
     */

    public static function getIp() {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && !empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_CLIENT_IP"]) && !empty($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
        return $realip;
    }

    /*
     * 获取字符串的字符个数
     */

    public static function pstrLen($str) {
        global $charset;
        $charset = strtolower($charset);
        $ll = $charset == 'gbk' ? 1 : 2;

        $strlen = strlen($str);
        $clen = 0;

        for ($i = 0; $i < $strlen; $i++, $clen++) {
            if ($clen >= $strlen)
                break;
            if (ord(substr($str, $i, 1)) > 128) {
                $i += $ll;
            }
        }
        return $clen;
    }

    /*
     * 魔术引用
     */

    public static function strSlash(&$data) {
    
          $add = 1;
          if(phpversion() >= '5.3.0' && function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
          $add = 0;
          }
          if($add) {
          if (is_array($data)) {
          foreach ($data as $k => $v) {
          $data[$k] = addslashes($v);
          }
          } else {
          $data = addslashes($data);
          }
          }
       
         /*
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = mysql_escape_string($v);
            }
        } else {
            $data = mysql_escape_string($data);
        }
        */

        return $data;
    }

}

<?php
/**
 *	[geetest���ӹ���(gtcheck.{modulename})] (C)2015-2099 Powered by .
 *	Version: v1.0
 *	Date: 2015-4-30 03:18
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
// require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'entry.class.php';
C::import('entry','plugin/gtcheck/lib');

class plugin_gtcheck{
    
}

class plugin_gtcheck_forum extends plugin_gtcheck{   
    //bbs topic submit

    function post_newthread_gtcheck_submit_message(){
        global $tid, $pid, $message;
        if(!($tid && $pid && trim($message)!="") || !submitcheck('topicsubmit')){
        file_put_contents("/www/discuz/discuz_30_UTF8/upload/source/plugin/post.txt", $tid."\r\n", FILE_APPEND);
            return array();
        } 
        $entry = & entry::singleton();
        $entry->appTypeKey = "bbs";
        $entry->textid = intval($pid);
        $entry->tid = intval($tid);
        file_put_contents("/www/discuz/discuz_30_UTF8/upload/source/plugin/post.txt", $entry->tid."\r\n", FILE_APPEND);
        $entry->run();
        return array();

        // return $data;
    }
}

?>
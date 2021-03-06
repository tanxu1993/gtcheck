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

class plugin_gtcheck_forum  {   
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
    }

    //bbs reply submit
    function post_newreply_gtcheck_submit_message(){
        global $pid, $message, $_G;
        $tid = intval($_REQUEST['tid']);
        if(!$tid) $tid = $_G['tid'];
        if(!($tid && $pid && trim($message)!="") || !submitcheck('replysubmit')){
            return array();
        } 
        file_put_contents("/www/discuz/discuz_30_UTF8/upload/source/plugin/post.txt", var_export($pid."|".$tid, true)."\r\n", FILE_APPEND);
        $entry = & entry::singleton();
        $entry->appTypeKey = "bbs";
        $entry->textid = intval($pid);
        $entry->tid = intval($tid);
        $entry->run();
        return array();
    }

    //bbs topic or reply edit submit
    function post_editpost_gtcheck_submit_message(){
        global $pid, $message, $_G;
        $tid = intval($_REQUEST['tid']);
        if(!$tid) $tid = $_G['tid'];
        if(!($tid && $pid && trim($message)!="") || !submitcheck('editsubmit') || !empty($_G['gp_delete'])){
            return array();
        } 
        file_put_contents("/www/discuz/discuz_30_UTF8/upload/source/plugin/post.txt", var_export($pid."|".$tid, true)."\r\n", FILE_APPEND);
        $entry = & entry::singleton();
        $entry->appTypeKey = "bbs";
        $entry->textid = intval($pid);
        $entry->tid = intval($tid);
        $entry->type = "edit";
        $entry->run();
        return array();
    }
}

?>
<?php
/**
 *	[geetest���ӹ���(gtcheck.{modulename})] (C)2015-2099 Powered by .
 *	Version: v1.0
 *	Date: 2015-4-30 03:18
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_gtcheck_forum{   
    //bbs topic submit
    function post_newthread_gtcheck_submit_message(){
        global $tid, $pid, $message;
        if(!($tid && $pid && trim($message)!="") || !submitcheck('topicsubmit')){
            return array();
        } 
        $pid = intval($pid);
        $tid = intval($tid);
        $table = "forum_post";
        $data = array(
            'status' => -1, #Ĭ�ϲ�����
            'data' => array()
        );
        //��ȡ�����
        $post = C::t("#gtcheck#forum_post_plugin")->fetch_by_id($tid);
        if ($post['invisible'] == 0) {
            $data['status'] = 0;
        } elseif ($post['invisible'] == -1 || $post['invisible'] == -5) {#�������ɾ��״̬
            $data['status'] = 9; #����վ
        } else {
            $data['status'] = 1; #����
        }
        file_put_contents("/home/tanxu/www/post.txt", $post['message'], FILE_APPEND);
        // return $data;
    }
}

?>
<?php 
// require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'purify.class.php';
C::import('purify','plugin/gtcheck/lib');
class entry {
    var $appTypeKey = null;#当前应用类型索引
    var $textid = 0;#当前数据记录id
    var $type = "new";#当前处理数据状态,编辑还是新增,默认新增
    var $pubaction = 1;#默认主题帖
    var $tid = 0;#主题帖id
    var $purify;#处理对象代理
    var $syncbbs = 0;
    var $idtype = "";

    function &singleton(){
        static $entryobj;
        if(empty($entryobj)){
            $entryobj = new entry();
        }
        return $entryobj;
    }
    function run (){
        global $_G;
        //默认应用类型索引
        if(!$this->appTypeKey){
            $this->appTypeKey = "blog";
        } 
        //初始化工作类
        $this->purify = & purify::singleton($this->appTypeKey);
        // #设置帖子id
        $this->purify->textid = intval($this->textid);
        $this->purify->syncbbs = $this->syncbbs;
        $this->purify->idtype = $this->idtype;
        // 初始化缓存表数据模板
        $postData = array(
            "textid" => $this->purify->textid,
            "idtype" => $this->purify->idtype,
            "cidtype" => "",
            "classid" => 0,
            "title" => "",
            "message" => "",
            "userid" => $_G['uid'] ? $_G['uid'] : 0,
            "username" => $_G['username'],
            "groupid" => intval($_G['groupid']),
            "signature" => "",
            "ip" => helper::getIp(),
            "date" => date('Y-m-d H:i:s', time()),
            "pubaction" => $this->pubaction,//默认主题帖
            "tid" => $this->tid ? $this->tid : 0,
            "url" => "",
            "status" => 8#状态默认不存在
        );
        // 设置主题id
        $this->purify->appTypeKey = $this->appTypeKey;
        $this->purify->tid = $this->tid;
        $post = $this->purify->get_record();
        file_put_contents("/www/discuz/discuz_30_UTF8/upload/source/plugin/post.txt", "\r\n"."username=".$post['data']['username']."\r\n"."status=".$post['status']."\r\n" ."time=".date('Y-m-d H:i:s', time())."\r\n", FILE_APPEND);
        if ($post['status'] == -1) {
            sleep(1);#延缓一秒,应对主从数据库延迟问题
            $post = $this->purify->get_record();
        }
        #如果帖子存在
        if ($post['data']['status'] != -1) {
        //获得真实缓存表数据
            $postData = $this->purify->replaceCache($postData, $post);
        // file_put_contents("/www/discuz/discuz_30_UTF8/upload/source/plugin/post.txt", "postdata=".http_build_query($postData)."\r\n", FILE_APPEND);
        }
        $this->purify->run($postData);
        // file_put_contents("/home/tanxu/www/post.txt", $postData['ip']."\r\n" , FILE_APPEND);
        // #获取数据信息
    }


}


 ?>
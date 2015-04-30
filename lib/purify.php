<?php 
// require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helper.class.php';
C::import('helper','plugin/gtcheck/lib');

class purify {
    var $appTypeKey = "";
    var $app_type = "";
    var $idtype = 0;
    var $textid = 0;
    var $tid = "";
    var $curCfg = array();
    //instance object
    function &singleton($appTypeKey = "") {
        static $purifyobj;
        if (empty($purifyobj)) {
            $purifyobj = new purify($appTypeKey);
        }
        return $purifyobj;
    }

    function get_record(){
        //获取结果集 
        //get_record
        #单条查询,初始化返回值数组
        $data = array(
            'status' => -1, #默认不存在
            'data' => array()
        );
        C::t("#gtcheck#forum_post_plugin")->tid = $this->tid;
        // file_put_contents("/home/tanxu/www/post.txt", $this->tid.'/', FILE_APPEND);
        $post = C::t("#gtcheck#forum_post_plugin")->fetch_by_id($this->tid);
        if ($post['invisible'] == 0) {
            $data['status'] = 0;
        } elseif ($post['invisible'] == -1 || $post['invisible'] == -5) {#如果处于删除状态
            $data['status'] = 9; #回收站
        } else {
            $data['status'] = 1; #待审
        }
        // file_put_contents("/home/tanxu/www/post.txt", $post, FILE_APPEND);
        $data['data'] = $post;
        // file_put_contents("/home/tanxu/www/post.txt", $data['data']['status'], FILE_APPEND);
        return $data;
    }
    // function _init($appTypeKey) {
    //     if (!$appTypeKey || empty($appTypeKey)) {
    //         return true;
    //     }
    //     $this->appTypeKey = trim($appTypeKey);
    //     $this->app_type = $this->config['app_type_arr'][$appTypeKey];
    //     $this->idtype = intval($this->idtypeArr[$appTypeKey]);
    //     $this->curCfg = $this->curCfgArr[$this->idtype];
    //     return true;
    // }
}

 ?>
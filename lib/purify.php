<?php 
// require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helper.class.php';
C::import('helper','plugin/gtcheck/lib');

class purify {
    var $appTypeKey = "";
    var $app_type = "";
    var $idtype = 0;
    var $textid = 0;
    var $tid = "";
    var $timestamp = "";
    var $format = "json";
    var $curCfg = array();
    var $config = array();
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
        // C::t("#gtcheck#forum_post_plugin")->tid = $this->tid;
        // file_put_contents("/home/tanxu/www/post.txt", $this->tid.'/', FILE_APPEND);
        $post = C::t("#gtcheck#forum_post_plugin")->fetch_by_id($this->textid);
        // file_put_contents("/home/tanxu/www/post.txt", print_r($post), FILE_APPEND);
        if ($post['invisible'] == 0) {
            $data['status'] = 0;
        } elseif ($post['invisible'] == -1 || $post['invisible'] == -5) {#如果处于删除状态
            $data['status'] = 9; #回收站
        } else {
            $data['status'] = 1; #待审
        }
        $data['data'] = $post;
        // file_put_contents("/home/tanxu/www/post.txt", $data['data']['status'], FILE_APPEND);
        return $data;
    }

    function replaceCache($postData, $post) {
        $data = $post['data'];
        $status = $post['status'];

        //处理状态值
        $postData['status'] = $status;
        //处理评论id类型
        $postData['cidtype'] = $data['cidtype'] ? $data['cidtype'] : "";
        //处理类别id
        $postData['classid'] = $data['classid'] ? $data['classid'] : 0;
        $urlArr = @include DISCUZ_ROOT.'source/plugin/gtcheck/config/configurl.php';

        //处理url[引入配置文件]
        // $urlArr = require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.url.php';
        #评论url子处理
        // if ($this->appTypeKey == 'comment') {
        //     $url = $urlArr['comment'][$data['cidtype']];
        // } else {
            $url = $urlArr[$this->appTypeKey];
        // file_put_contents("/home/tanxu/www/post.txt", "appTypeKey =".$this->appTypeKey."/urlArr=".$urlArr."/url=".$url, FILE_APPEND);
        // }
        //标签替换
        $url = str_replace("{userid}", $data['userid'], $url);
        $url = str_replace("{textid}", $data['textid'], $url);
        if (isset($data['id'])) {
            $url = str_replace("{id}", $data['id'], $url);
        }
        if ($postData['tid']) {
            $url = str_replace("{tid}", $postData['tid'], $url);
        }
        $postData['url'] = $url;
        //首贴或回复贴
        if (isset($data['first'])) {
            $postData['pubaction'] = $data['first'] ? 1 : 2;
        }

        #如果是回复贴则定位到回复贴地址 author:hanyong data:2012-09-10
        if(($this->appTypeKey == 'bbs' || $this->appTypeKey == 'group') && $postData['pubaction'] == 2){
            $postData['url'] = "forum.php?mod=redirect&goto=findpost&ptid={$postData['tid']}&pid={$data['textid']}&fromuid={$data['userid']}";
        }

        // #论坛回复贴同步提交主题标题
        // if ($postData['pubaction'] == 2 && $this->config['need_title'] && $this->curCfg['table'] == "post") {
        //     $threadTitle = C::t($this->tableKey['thread'])->fetch_first_by_id($this->tid, "subject");
        //     $data['message'] = $data['title'] . "\n" . $data['message'];
        //     $data['title'] = $threadTitle;
        // }
        // #分享同步提交链接地址
        // if ($this->appTypeKey == "share" && isset($data['body_data'])) {
        //     $linkArr = unserialize($data['body_data']);
        //     if ($linkArr['link']) {
        //         $data['message'] = $linkArr['link'] . "\n" . $data['message'];
        //     }
        // }
        // #论坛提交用户签名
        // if ($data['userid'] && $this->config['need_signature'] && $this->curCfg['table'] == "post") {
        //     $data['signature'] = C::t($this->tableKey['member_field_forum'])->fetch_first_by_id($data['userid'], "sightml");
        // }
        // #获取图片信息
        // $data = $this->replace_attachment($data);

        $postData['title'] = $data['title'] ? helper::strSlash($data['title']) : "";
        $postData['message'] = helper::strSlash($data['message']);
        $postData['userid'] = $data['userid'] ? $data['userid'] : $postData['userid'];
        $postData['username'] = $data['username'] ? $data['username'] : $postData['username'];
        $postData['signature'] = $data['signature'] ? helper::strSlash($data['signature']) : "";
        $postData['ip'] = $data['ip'] ? $data['ip'] : $postData['ip'];
        $postData['date'] = date('Y-m-d H:i:s', $data['dateline']);
        // file_put_contents("/home/tanxu/www/post.txt", "\r\n".http_build_query($postData), FILE_APPEND);
        return $postData;
    }

    function run(&$post){
        $this->config = @include DISCUZ_ROOT.'/source/plugin/gtcheck/config/config.php';
            // $id = $post['textid'];
            // $callPost = "sig=" . md5($this->config['app_key']);
            // $callPost .= "&textid={$id}&idtype={$post['idtype']}";
            // $callPost .= "&contentEx={$this->syncbbs}";
            // #调用代理页面，只POST不接收
            // $this->send_request($callUrl, $callPost, 0, 3, false);
        $params = $this->get_params($post);
        //发送请求
        $url = $this->config['predict_api'];
        $resp = $this->send_request($url, $params, 1, $this->config['timeout']);
        if ($this->format == 'json') {
            $retJson = json_decode($resp, true);
        }
        $result = (array) current(current(current($retJson)));
        $result['flag'] = intval($result['flag']); //识别结果
        // return 
        file_put_contents("/home/tanxu/www/post.txt", $result['flag'] , FILE_APPEND);
    }

    function send_request($url, $post = '', $limit = 1, $timeout = 10, $block = TRUE) {
        $return = '';
        $matches = parse_url($url);
        $host = $matches['host'];
        $path = $matches['path'] ? $matches['path'] . ($matches['query'] ? '?' . $matches['query'] : '') : '/';
        $port = !empty($matches['port']) ? $matches['port'] : 80;
        $cookie = '';
        $ip = '';
        if ($post) {
            $out = "POST $path HTTP/1.0\r\n";
            $out .= "Accept: */*\r\n";
            $out .= "Accept-Language: zh-cn\r\n";
            $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
            $out .= "Host: $host\r\n";
            $out .= 'Content-Length: ' . strlen($post) . "\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Cache-Control: no-cache\r\n";
            $out .= "Cookie: $cookie\r\n\r\n";
            $out .= $post;
        } else {
            $out = "GET $path HTTP/1.0\r\n";
            $out .= "Accept: */*\r\n";
            $out .= "Accept-Language: zh-cn\r\n";
            $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Cookie: $cookie\r\n\r\n";
        }
        if (function_exists('fsockopen')) {
            // $this->log('request url msg[host:' . $host . ',port:' . $port . ',path:' . $path . ']', 0, true);
            $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
        } else {
            $fp = null;
            $errstr = "function 'fsockopen' not exists";
        }
        //请求失败
        if (!$fp) {
            // $this->log("socket failure,message:" . $errstr, 1);
            return false;
        }
        // $this->log('socket request ok', 0, true);
        $r = fwrite($fp, $out);
        //socket写失败
        if (!$r) {
            @fclose($fp);
            // $this->log('socket write failure', 1);
            return false;
        }
        //阻塞或非阻塞模式
        stream_set_blocking($fp, $block);
        //timeout
        stream_set_timeout($fp, $timeout);
        //只写不读,直接返回,注意,此时没有关闭socket线程
        if ($limit == 0) {
            usleep(100000);
            @fclose($fp);
            // $this->log('socket write ok and close socket', 0, true);
            return true;
        }
        // $this->log('prepare get data from socket', 0, true);
        //从socket管道读数据头
        while (!feof($fp)) {
            if (($header = @fgets($fp)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
            $state = intval(str_replace(array(' ', 'http/1.0'), '', strtolower($header)));
            // if ($state && 302 < $state) {
            //     $this->log("socket header:" . str_replace(array("\r\n", "\n"), '', $header), 0, true);
            // }
        }
        $return = ""; #return value
        //从socket管道读数据
        while (!feof($fp) && $limit > 0) {
            $data = fread($fp, ($limit == 1 || $limit > 8192 ? 8192 : $limit));
            $return .= $data;
            $limit -= strlen($data);
        }
        //获得meta数据
        $status = stream_get_meta_data($fp);
        @fclose($fp);
        //超时
        if ($status['timed_out']) {
            // $this->log('socket read timeout', 1);
            return false;
        }
        return $return;
    }

    // 构造XML数据，接口不支持批量，目前都是单条
    function build_xml_data(&$post) {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';
        $xml .= '<contents>';
        $xml .= '<content>';
        $xml .= '<class><![CDATA[' . $post['classid'] . ']]></class>';
        $xml .= '<textId><![CDATA[' . $post['textid'] . ']]></textId>';
        $xml .= '<url><![CDATA[' . $this->config['bbs_root'] . $post['url'] . ']]></url>';
        $xml .= '<title><![CDATA[' . stripslashes($post['title']) . ']]></title>';
        $xml .= '<text><![CDATA[' . stripslashes($post['message']) . ']]></text>';
        $xml .= '<author><![CDATA[' . $post['username'] . ']]></author>';
        $xml .= '<userId><![CDATA[' . $post['userid'] . ']]></userId>';
        $xml .= '<ip><![CDATA[' . $post['ip'] . ']]></ip>';
        $xml .= '<pubDate><![CDATA[' . $post['date'] . ']]></pubDate>';
        $xml .= '<threadId><![CDATA[' . $post['tid'] . ']]></threadId>';
        $xml .= '<authorEx><![CDATA[' . $post['authorex'] . ']]></authorEx>';
        $xml .= '<contentEx><![CDATA[' . $post['contentex'] . ']]></contentEx>';
        $xml .= '<structureEx><![CDATA[' . $post['structureex'] . ']]></structureEx>';
        $xml .= '<rules><![CDATA[' . $post['rules'] . ']]></rules>';
        $xml .= '<pubAction><![CDATA[' . $post['pubaction'] . ']]></pubAction>';
        $xml .= '<signature><![CDATA[' . $post['signature'] . ']]></signature>';
        $xml .= '<actorUser><![CDATA[' . $post['actorUser'] . ']]></actorUser>'; # data：2012-08-30 actorUser角色引擎等级信息
        $xml .= '<contentEx><![CDATA[' . $this->syncbbs . ']]></contentEx>';
        $xml .= '</content>';
        $xml .= '</contents>';

        $xml = $this->convert($xml);
        return $xml;
    }    //get params
    function get_params(&$post) {
        $xml = $this->build_xml_data($post);
        $this->start_time = helper::float_time();
        $this->timestamp = time();
        $transId = md5(rand(1, 10000));
        $transId .= md5($this->start_time . $post['textid'] . $post['idtype']);

        $params = 'appType=' . $this->app_type;
        $params .= '&appid=' . $this->config['app_id'];
        $params .= '&format=' . $this->format;
        $params .= '&param=' . $xml;
        $params .= '&time=' . $this->timestamp;
        $params .= '&transId=' . $transId;
        $params .= '&v=2.0';

        $sig = md5($params . $this->config['app_key']);

        $params = 'appType=' . $this->app_type;
        $params .= '&appid=' . $this->config['app_id'];
        $params .= '&format=' . $this->format;
        $params .= '&param=' . urlencode($xml);
        $params .= '&time=' . $this->timestamp;
        $params .= '&transId=' . $transId;
        $params .= '&v=2.0';
        $params .= '&sig=' . $sig;
        return $params;
    }


    function convert($text, $des = "send") {
        global $_G;
        include DISCUZ_ROOT .  '/config /config_global.php';
        $charset = $_config['output']['charset'];
        if ('UTF-8' != strtoupper(trim($charset))) {
            if ($des == 'send') {
                if (function_exists('mb_convert_encoding')) {
                    $text = mb_convert_encoding($text, 'utf-8', 'gbk');
                } else {
                    $text = iconv('gbk', 'utf-8//ignore', $text);
                }
            } else {
                if (function_exists('mb_convert_encoding')) {
                    $text = mb_convert_encoding($text, 'gbk', 'utf-8');
                } else {
                    $text = iconv('utf-8', 'gbk//ignore', $text);
                }
            }
        }
        return $text;
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
<?php
/*
* 海量帖子净化插件的配置文件
* 可根据需要进行适当的配置
*/
return array(
########-以下配置需要人工修改-########
    
    #客户唯一标识，注册海量公司“保10洁”服务后系统分配，请谨慎更改
    "app_id" => "790",
    #客户序列号，注册海量公司“保10洁”服务后系统分配，请谨慎更改
    "app_key" => "0cea7776d4414e8b41647f712f0d69ea6eea8134",
    #论坛访问web根目录全路径,例如:http://www.bbs.com:8080/bbs/
    "bbs_root" => "http://test.geetest.com/discuz/discuz_30_UTF8/upload/",
    
########-以下配置由系统默认设置，一般不需人工修改。修改前请理解每个配置项的含义-########

    #对接保10洁服务的客户端类型标识枚举序列
    "app_type_arr" => array(
        "blog" => "blog",
        "doing" => "doing",
        "album" => "pic",
        "share" => "share",
        "comment" => "comment",
        "group" => "group",
        "bbs" => "bbs",
        "follow" => "follow",
    ),
    #海量公司“保10洁”服务的净化接口地址，请谨慎更改
    "predict_api" => "http://api.bao10jie.net/rest/purify/predict",
    #海量公司“保10洁”服务的反馈接口地址，请谨慎更改
    "feedback_api" => "http://api.bao10jie.net/rest/purify/train",
    #该插件调用接口时的最大延迟时间，当大于这个时间时，插件放弃等待接口。一般为10秒钟，单位：秒
    "timeout" => "10",
    #小于N个字符后，将不提交数据
    "str_num" => "0",
    #单个处理脚本等待超时时间,单位:秒
    "time_limit" => "60",
    #每次查询抛出的标引处理脚本的数量
    "call_num" => "5",
    #连接失败重试次数
    "try_num" => "1",
    #该插件调用接口前的停留时间，不可以为0，单位：秒
    "lag_showtime" => "1",
    #回复帖标引时是否需要同步提交主题标题, 0:不提交,1:提交, 默认提交
    "need_title" => "1",
    #同步提交用户签名
    "need_signature" => "1",
    #帖子标引记录表有效操作保留时间,单位：天,最短15天
    "cache_day" => "15",
    #标引为垃圾的帖子的处理方式,0:删除,1:待审
    "check_all_arr" => array(
        'bbs' => 0,
        'blog' => 0,
        'other' => 1,
    ),
    #置顶贴是否发送
    "send_top" => "1",
    #审核通过的帖子是否发送到特定端口,0:不发送,1:发送
    "normal_to_port" => "0",
    #要发送的制定端口地址
    "port_addr" => "",
    #群组帖子是否同时发送, 0:不发送,1:发送
    "group_send" => "0",
    #是否开启附件独立域名,默认为 0 , 1:表示开启附件独立域名(若开启请填写附件域名地址)
    "attach_conf" => "0",
    #附件域名地址
    "attach_domain" => ""
);
?>
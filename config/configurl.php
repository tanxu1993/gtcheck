<?php

/*
 * 海量帖子净化插件的配置文件
 * 可根据需要进行适当的配置
 */
//各种应用数据的链接url模板
return array(
########-以下配置由系统默认设置，一般不需人工修改。修改前请理解每个配置项的含义-########
    'blog' => "home.php?mod=space&uid={userid}&do=blog&id={textid}",
    'album' => "home.php?mod=space&uid={userid}&do=album&picid={textid}",
    'share' => "home.php?mod=space&uid={userid}&do=share&id={textid}",
    'doing' => "home.php?mod=space&uid={userid}&do=doing",
    'group' => "forum.php?mod=viewthread&tid={tid}",
    'bbs' => "forum.php?mod=viewthread&tid={tid}",
    'follow' => "home.php?mod=follow&uid={userid}&do=view&from=space",
    'comment' => array(
        'blogid' => "home.php?mod=space&uid={userid}&do=blog&id={id}",
        'picid' => "home.php?mod=space&uid={userid}&do=album&picid={id}",
        'sid' => "home.php?mod=space&uid={userid}&do=share&id={id}",
        'uid' => "home.php?mod=space&uid={userid}&do=wall"
    )
);
?>
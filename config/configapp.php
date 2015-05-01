<?php
/*
 * 运行时配置数组
 */
return array(
    1 => array(
        "idtype" => "blogid",
        "table" => "blog",
        "check" => array(
            "credit" => array(
                "method" => "exec",
                "action" => "publishblog",
                "type" => "blogs"
            )
        ),
        "pass" => array(
            "credit" => array(
                "method" => "exec",
                "action" => "publishblog",
                "type" => "blogs"
            )
        ),
        "delete" => array(
            "credit" => array(
                "method" => "update",
                "action" => "publishblog",
                "type" => "blogs"
            )
        )
    ),
    2 => array(
        "idtype" => "doid",
        "table" => "doing",
        "delete" => array(
            "credit" => array(
                "method" => "update",
                "action" => "doing",
                "type" => "doings"
            )
        )
    ),
    3 => array(
        "idtype" => "picid",
        "table" => "pic",
        "delete" => array(
            "method" => "pic"
        )
    ),
    4 => array(
        "idtype" => "sid",
        "table" => "share",
        "pass" => array(
            "method" => "share"
        ),
        "delete" => array(
            "credit" => array(
                "method" => "update",
                "action" => "createshare",
                "type" => "sharings"
            )
        )
    ),
    5 => array(
        "idtype" => "cid",
        "table" => "comment",
        "delete" => array(
            "credit" => array(
                "method" => "update",
                "action" => "comment"
            ),
            "method" => "comment"
        )
    ),
    6 => array(
        "idtype" => "pid",
        "table" => "post",
        "check" => array(
            "method" => "group"
        ),
        "pass" => array(
            "method" => "group"
        ),
        "delete" => array(
            "method" => "group"
        )
    ),
    7 => array(
        "idtype" => "pid",
        "table" => "post",
        "check" => array(
            "method" => "group"
        ),
        "pass" => array(
            "method" => "group"
        ),
        "delete" => array(
            "method" => "group"
        )
    ),
    8 => array(
        'idtype' => 'pid',
        'table' => 'follow_feed',
        "check" => array(
            "method" => "group"
        ),
        "pass" => array(
            "method" => "group"
        ),
        "delete" => array(
            "method" => "group"
        ),
    ),
);
?>


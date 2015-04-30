<?php

/**
 * 安装保10洁帖子净化插件
 */
if (!defined('IN_DISCUZ')) exit('Access Denied');

require DISCUZ_ROOT . '/config/config_global.php';
require_once DISCUZ_ROOT . '/source/class/class_core.php';

require_once DISCUZ_ROOT.'/source/function/function_core.php';

define('CHARSET', $_config['output']['charset']);

$tb_post = DB::table('gtcheck_post');
$tb_cache = DB::table('gtcheck_cache');
//处理字符集
$tb_charset = "";
if(trim(CHARSET)!=""){
    $tb_charset = "DEFAULT CHARSET=".(strtolower(trim(CHARSET))=="gbk" ? "gbk" : "utf8");
}

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `gtcheck_post` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT 'primary key,autoincrement',
  `textid` int(10) unsigned NOT NULL COMMENT 'content item id,for example,blogid of home blog',
  `idtype` tinyint(2) unsigned NOT NULL COMMENT 'content item id type,for example:blogid of home blog is equal 0',
  `cidtype` varchar(10) default NULL COMMENT 'comment idtype',
  `classid` mediumint(8) unsigned NOT NULL default '0' COMMENT 'content item type id',
  `tid` int(10) unsigned default '0' COMMENT 'thread id',
  `userid` mediumint(8) unsigned NOT NULL default '0' COMMENT 'item author id',
  `username` varchar(15) default NULL COMMENT 'item author name',
  `sig` tinyint(2) NOT NULL default '-1' COMMENT 'return mark status value by bao10jie',
  `sendtime` int(10) unsigned NOT NULL default '0' COMMENT 'send time of content',
  `edittime` int(10) unsigned NOT NULL default '0' COMMENT 'edit time of content',
  `updatetime` int(10) unsigned NOT NULL default '0' COMMENT 'update time of mark status value from bao10jie',
  `status` tinyint(2) NOT NULL default '-3' COMMENT 'content process status, enum type,-3:not mark;-2:mark only;-1:deleted;0:wait check;1:passed'' ',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `textid` (`textid`,`idtype`),
  KEY `sendtime` (`sendtime`),
  KEY `edittime` (`edittime`),
  KEY `updatetime` (`updatetime`)
);
CREATE TABLE IF NOT EXISTS `gtcheck_cache` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT 'primary key,autoincrement',
  `textid` int(10) unsigned NOT NULL default '0' COMMENT 'content item id,for example,blogid of home blog',
  `idtype` tinyint(2) unsigned NOT NULL default '0' COMMENT 'content item id type,for example:blogid of home blog is equal 0',
  `cidtype` varchar(10) default NULL COMMENT 'comment idtype',
  `classid` mediumint(8) unsigned NOT NULL default '0' COMMENT 'content item type id',
  `title` varchar(80) default NULL COMMENT 'item title',
  `message` mediumtext NOT NULL COMMENT 'item content',
  `userid` mediumint(8) unsigned NOT NULL default '0' COMMENT 'item author id',
  `username` varchar(15) default NULL COMMENT 'item author name',
  `groupid` mediumint(8) unsigned NOT NULL default '0' COMMENT 'group id',
  `signature` mediumtext NOT NULL COMMENT 'user sig html',
  `ip` varchar(15) default NULL COMMENT 'from ip',
  `date` varchar(20) default NULL COMMENT 'pubdate',
  `url` varchar(255) default NULL COMMENT 'view url',
  `pubaction` tinyint(1) unsigned NOT NULL default '1' COMMENT 'thread 1,reply 2',
  `tid` int(10) unsigned default '0' COMMENT 'thread id',
  `status` tinyint(1) unsigned NOT NULL default '0' COMMENT 'check status,etc,0:pass,1:check',
  `timestamp` int(10) NOT NULL default '0' COMMENT 'wait timestamp for markup',
  `locked` tinyint(1) NOT NULL default '0' COMMENT 'is locked? 0:no,1:yes',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `textid` (`textid`,`idtype`),
  KEY `timestamp` (`timestamp`)
);
EOF;
runquery($sql);

$finish = true;
?>
<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_forum_post_plugin extends discuz_table {

    public $tid;
    private static $_tableid_tablename = array();
    public $_fields;

    public function __construct() {
        $this->_table = 'forum_post';
        $this->_pk = 'pid';
        $this->_fields = "pid AS textid,fid AS classid,tid,authorid AS userid,author AS username,subject AS title,message,useip AS ip,dateline,status AS poststatus,first,attachment,invisible";
        parent::__construct();
    }

    public static function get_tablename($tableid, $primary = 0) {
        list($type, $tid) = explode(':', $tableid);
        if (!isset(self::$_tableid_tablename[$tableid])) {
            if ($type == 'tid') {
                self::$_tableid_tablename[$tableid] = self::getposttablebytid($tid, $primary);
            } else {
                self::$_tableid_tablename[$tableid] = self::getposttable($type);
            }
        }
        return self::$_tableid_tablename[$tableid];
    }

    public function fetch_all_by_id($idArr) {
        if (!$idArr || !is_array($idArr)) {
            return array();
        }
        $sqlTpl = "SELECT %i FROM %t WHERE pid IN (%i)";
        return DB::fetch_all($sqlTpl, array($this->_fields, self::get_tablename("tid:" . $this->tid), implode(",", $idArr)));
    }

    public function fetch_by_id($id) {
        if (!$id) {
            return array();
        }
        $sqlTpl = "SELECT %i FROM %t WHERE pid=%d";
        return DB::fetch_first($sqlTpl, array($this->_fields, self::get_tablename("tid:" . $this->tid), $id));
    }

    public function fetch_all($fields, $where) {
        $sqlTpl = "SELECT %i FROM %t WHERE %i";
        return DB::fetch_all($sqlTpl, array($fields, self::get_tablename("tid:" . $this->tid), $where));
    }

    public function update_by_id($data,$where) {
        return DB::update($this->_table,$data, $where);
    }

    public function update($data, $where, $idtype, $idvalArr) {
        $return = DB::update(self::get_tablename("tid:" . $this->tid), $data, $where);
        if ($return && $this->_allowmem) {
            //foreach($idvalArr as $idval){
            $this->update_cache($idvalArr, $idtype, $data);
            //}
        }
        return $return;
    }

    public function update_cache($id, $idtype, $data, $condition = array(), $glue = 'merge') {
        if (!$this->_allowmem)
            return;

        if ($idtype == 'tid') {
            $memorydata = $this->fetch_cache($id, $this->_pre_cache_key . 'tid_');
            if (!$memorydata) {
                return;
            }
            if (!is_array($id)) {
                $memorydata = array($id => $memorydata);
                $id = (array) $id;
            }
            foreach ($id as $v) {
                if (!$memorydata[$v]) {
                    continue;
                }
                foreach ($memorydata[$v] as $pid => $post) {
                    $updateflag = true;
                    if ($condition) {
                        foreach ($condition as $ck => $cv) {
                            if ($cv !== null && !in_array($post[$ck], (array) $cv)) {
                                $updateflag = false;
                                break;
                            }
                        }
                    }
                    if ($updateflag) {
                        if ($glue == 'merge') {
                            $memorydata[$v][$pid] = array_merge($post, $data);
                        } else {
                            foreach ($data as $dk => $dv) {
                                $memorydata[$v][$pid][$dk] = helper_util::compute($memorydata[$v][$pid][$dk], $dv, $glue);
                            }
                        }
                    }
                }
                $this->store_cache($v, $memorydata[$v], $this->_cache_ttl, $this->_pre_cache_key . 'tid_');
            }
        } elseif ($idtype == 'pid') {
            $memorytid = array();
            $query = DB::query('SELECT pid, tid FROM %t WHERE ' . DB::field('pid', $id), array(self::get_tablename("tid:" . $this->tid)));
            while ($post = DB::fetch($query)) {
                $memorytid[$post['pid']] = $post['tid'];
            }
            $memorydata = $this->fetch_cache($memorytid, $this->_pre_cache_key . 'tid_');
            if (!$memorydata) {
                return;
            }
            if (!is_array($id)) {
                $id = (array) $id;
            }
            foreach ($id as $v) {
                if ($memorydata[$memorytid[$v]][$v]) {
                    $updateflag = true;
                    if ($condition) {
                        foreach ($condition as $ck => $cv) {
                            if ($cv !== null && !in_array($memorydata[$memorytid[$v]][$v][$ck], (array) $cv)) {
                                $updateflag = false;
                                break;
                            }
                        }
                    }
                    if ($updateflag) {
                        if ($glue == 'merge') {
                            $memorydata[$memorytid[$v]][$v] = array_merge($memorydata[$memorytid[$v]][$v], $data);
                        } else {
                            foreach ($data as $dk => $dv) {
                                $memorydata[$memorytid[$v]][$v][$dk] = helper_util::compute($memorydata[$memorytid[$v]][$v][$dk], $dv, $glue);
                            }
                        }
                    }
                }
            }
            foreach ($memorydata as $tid => $postlist) {
                $this->store_cache($tid, $postlist, $this->_cache_ttl, $this->_pre_cache_key . 'tid_');
            }
        } elseif ($idtype == 'fid') {
            
        }
    }

    public function delete($where) {
        return DB::delete(self::get_tablename("tid:" . $this->tid), $where);
    }

    public static function getposttable($tableid = 0, $prefix = false) {
        global $_G;
        $tableid = intval($tableid);
        if ($tableid) {
            loadcache('posttableids');
            $tableid = $_G['cache']['posttableids'] && in_array($tableid, $_G['cache']['posttableids']) ? $tableid : 0;
            $tablename = 'forum_post' . ($tableid ? "_$tableid" : '');
        } else {
            $tablename = 'forum_post';
        }
        if ($prefix) {
            $tablename = DB::table($tablename);
        }
        return $tablename;
    }

    public static function getposttablebytid($tids, $primary = 0) {

        $isstring = false;
        if (!is_array($tids)) {
            $thread = getglobal('thread');
            if (!empty($thread) && isset($thread['posttableid']) && $tids == $thread['tid']) {
                return 'forum_post' . (empty($thread['posttableid']) ? '' : '_' . $thread['posttableid']);
            }
            $tids = array(intval($tids));
            $isstring = true;
        }
        $tids = array_unique($tids);
        $tids = array_flip($tids);
        if (!$primary) {
            loadcache('threadtableids');
            $threadtableids = getglobal('threadtableids', 'cache');
            empty($threadtableids) && $threadtableids = array();
            if (!in_array(0, $threadtableids)) {
                $threadtableids = array_merge(array(0), $threadtableids);
            }
        } else {
            $threadtableids = array(0);
        }
        $tables = array();
        $posttable = '';
        foreach ($threadtableids as $tableid) {
            $threadtable = $tableid ? "forum_thread_$tableid" : 'forum_thread';
            $query = DB::query("SELECT tid, posttableid FROM " . DB::table($threadtable) . " WHERE tid IN(" . dimplode(array_keys($tids)) . ")");
            while ($value = DB::fetch($query)) {
                $posttable = 'forum_post' . ($value['posttableid'] ? "_$value[posttableid]" : '');
                $tables[$posttable][$value['tid']] = $value['tid'];
                unset($tids[$value['tid']]);
            }
            if (!count($tids)) {
                break;
            }
        }
        if (empty($posttable)) {
            $posttable = 'forum_post';
            $tables[$posttable] = array_flip($tids);
        }
        return $isstring ? $posttable : $tables;
    }

}

?>
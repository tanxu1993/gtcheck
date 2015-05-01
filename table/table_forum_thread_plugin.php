<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_forum_thread_plugin extends discuz_table {

    public $_fields;
    public function __construct() {
        $this->_table = 'forum_thread';
        $this->_pk = 'tid';
        $this->_pre_cache_key = 'forum_thread_';
        $this->_fields = "sid AS textid,title_template AS title,body_general AS message,uid AS userid,username,status,dateline,type,itemid,body_template,body_data,image,image_link";
        parent::__construct();
    }

    public function get_table_name($tableid = 0) {
        $tableid = intval($tableid);
        return $tableid ? "{$this->_table}_$tableid" : $this->_table;
    }

    public function fetch_first_by_id($id, $field) {
        if (!$id) {
            return "";
        }
        $sqlTpl = "SELECT %i FROM %t WHERE tid=%d";
        return DB::result_first($sqlTpl, array($field, $this->get_table_name(), $id));
    }

    public function update($data, $where, $tidArr) {
        $num = DB::update($this->get_table_name(), $data, $where);
        if($num){
               //$this->update_batch_cache((array)$tidArr, $data);
               $this->update_batch_cache((array)$tidArr, $data,null,$this->_pre_cache_key);
        }
        return $num;
    }

}

?>
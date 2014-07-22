<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class SystemLogModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('id', 'user_id','remark',
	                          'create_time','create_by_id','update_time','update_by_id','active');
    protected $pk     = 'id';
}

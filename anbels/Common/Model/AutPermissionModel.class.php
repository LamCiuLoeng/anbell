<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class AuthPermissionModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('id', 'name',
							   'create_time','create_by_id','update_time','update_by_id','active');
    protected $pk     = 'id';
}

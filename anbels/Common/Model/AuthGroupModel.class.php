<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class AuthGroupModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('id', 'name', 'display_name',
							   'create_time','create_by_id','update_time','update_by_id','active');
    protected $pk     = 'id';
}

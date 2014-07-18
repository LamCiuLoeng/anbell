<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class AuthUserModel extends Model {
    protected $tablePrefix = 'anbels_'; 
    // protected $tableName = 'auth_user';
    // protected $trueTableName = 'anbels_auth_user';
	protected $fields = array('id', 'name', 'password','last_login_time','salt',
	                          'create_time','create_by_id','update_time','update_by_id','active');
    protected $pk     = 'id';
}

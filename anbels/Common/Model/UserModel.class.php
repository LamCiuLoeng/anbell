<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class UserModel extends Model {
    protected $tablePrefix = 'anbels_'; 
    protected $tableName = 'auth_user';
    protected $trueTableName = 'anbels_auth_user';
	protected $fields = array('id', 'name', 'password','active','last_login_time');
    protected $pk     = 'id';
}

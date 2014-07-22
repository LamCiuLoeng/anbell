<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class SystemCurrentUserModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('user_key', 'time_stamp');
    protected $pk     = 'user_key';
}

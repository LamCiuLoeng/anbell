<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class MasterClassModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('id', 'school_id','grade','name','desc',
							   'create_time','create_by_id','update_time','update_by_id','active');
    protected $pk     = 'id';
}

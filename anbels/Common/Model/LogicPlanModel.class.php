<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class LogicPlanModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('id', 'name', 'school_id','grade','desc',
							  'create_time','create_by_id','update_time','update_by_id','active');
    protected $pk     = 'id';
}

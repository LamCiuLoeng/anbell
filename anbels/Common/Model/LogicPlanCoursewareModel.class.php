<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class LogicPlanCoursewareModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('id','category_id','plan_id', 'obj_id');
    protected $pk     = 'id';
}

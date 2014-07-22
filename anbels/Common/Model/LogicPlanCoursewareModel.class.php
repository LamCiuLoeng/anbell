<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class LogicPlanCoursewareModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('plan_id', 'courseware_id');
    protected $pk     = array('plan_id', 'courseware_id');
}

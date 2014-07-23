<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class LogicPlanCoursewareModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('user_id', 'class_id','role',);
    protected $pk     = array('user_id', 'class_id');
}

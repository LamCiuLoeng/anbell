<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class LogicPlanGameModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('id','plan_id','category_id','obj_id');
    protected $pk     =  'id';
}

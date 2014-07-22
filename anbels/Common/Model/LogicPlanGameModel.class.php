<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class LogicPlanGameModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('plan_id', 'game_id');
    protected $pk     = array('plan_id', 'game_id');
}

<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class MasterQuestionModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('id', 'category_id', 'content','correct_answer',
							  'answer01','answer01_content','answer02','answer02_content','answer03','answer03_content',
							  'answer04','answer04_content','answer05','answer05_content','answer06','answer06_content',
							  'answer07','answer07_content','answer08','answer08_content','answer09','answer09_content',
							  'answer10','answer10_content',
							   'create_time','create_by_id','update_time','update_by_id','active');
    protected $pk     = 'id';
}

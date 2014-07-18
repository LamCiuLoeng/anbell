<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class MasterCoursewareModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('id', 'name', 'category_id','desc','path','url',
							   'create_time','create_by_id','update_time','update_by_id','active');
    protected $pk     = 'id';
}

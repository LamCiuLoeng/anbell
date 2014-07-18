<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class MasterLocationModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('id', 'name', 'full_name','full_path_ids','parent_id',
							   'create_time','create_by_id','update_time','update_by_id','active');
    protected $pk     = 'id';
}

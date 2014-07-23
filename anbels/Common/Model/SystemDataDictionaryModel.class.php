<?php
namespace Common\Model;
use Think\Model;
/**
 * 
 */
class SystemDataDictionaryModel extends Model {
    protected $tablePrefix = 'anbels_';
	protected $fields = array('id', 'name','value');
    protected $pk     = 'id';
}

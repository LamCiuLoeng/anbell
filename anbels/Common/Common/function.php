<?php
define('FLAG_OK', 0);
define('MSG_OK', '操作成功。');
define('FLAG_NOT_EXIST', 1);
define('MSG_NOT_EXIST', '该记录不存在！');
define('FLAG_NOT_ALL_REQUIRED', 2);
define('MSG_NOT_ALL_REQUIRED', '没有提供必须的参数！');
define('FLAG_NOT_ALLOW', 3);
define('MSG_NOT_ALLOW', '非法操作！');
define('FLAG_NO_ACTION_TYPE',4);
define('MSG_NO_ACTION_TYPE', '没有该操作类型！');
define('FLAG_WRONG_PW', 5);
define('MSG_WRONG_PW', '该记录不存在！');
define('FLAG_WRONG_PARAMS', 6);
define('MSG_WRONG_PARAMS', '参数错误！');






function login_check($system_no,$password){
	$m['system_no'] = $system_no;
	if( !$m['system_no'] || is_null($m['system_no']) )
	{
		return array("flag" => FLAG_NOT_ALL_REQUIRED , "msg" => MSG_NOT_ALL_REQUIRED);
	}
	$m['active'] = 0;
	$User = M("AuthUser");
	$user = $User->where($m)->find();
	if(!$user || is_null($user)){
		return array("flag" => FLAG_NOT_EXIST , "msg" => MSG_NOT_EXIST);
	}	
	if(!$password || is_null($password)){
		return array("flag" => FLAG_NOT_ALL_REQUIRED, "msg" => MSG_NOT_ALL_REQUIRED);
	}
	$dbpw = $user['password'];
	$hashpw = crypt($password,"dingnigefei");
	if($dbpw != $hashpw){
		return array("flag" => FLAG_WRONG_PARAMS, "msg" => MSG_WRONG_PARAMS);
	}
	
	//log the last login time for the user
	$data = array('last_login_time' => mynow());
	$User-> where(array('id' => $user['id']))->setField($data);
	return array("flag" => 0 , "user" => $user);	
}



function mynow(){
	return date('Y-m-d H:i:s',time());
}



function mydto(){
	return array(
		'create_time' => mynow(),
		'update_time' => mynow(),
		'active' => 0,
		'create_by_id' => session('user_id',null),
		'update_by_id' => session('user_id',null),
	);
		
}


function generatekey()
{
		return uniqid('',TRUE);
}

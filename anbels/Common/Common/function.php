<?php
function login_check($name,$password){
	$m['name'] = $name;
	if( !$m['name'] || is_null($m['name']) )
	{
		return array("flag" => 1 , "msg" => '账户不能为空！');
	}
	$m['active'] = 0;
	$User = M("AuthUser");
	$user = $User->where($m)->find();
	if(!$user || is_null($user)){
		return array("flag" => 2 , "msg" => '该账户不存在！');
	}	
	if(!$password || is_null($password)){
		return array("flag" => 3, "msg" => '密码不能为空！');
	}
	$dbpw = $user['password'];
	$hashpw = crypt($password,"dingnigefei");
	if($dbpw != $hashpw){
		return array("flag" => 4, "msg" => '密码错误！');
	}
	return array("flag" => 0 , "user" => $user);	
}



function generatekey()
{
		return uniqid('',TRUE);
}

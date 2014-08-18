<?php
use Think\Auth;

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
define('MSG_WRONG_PW', '错误的密码！');
define('FLAG_WRONG_PARAMS', 6);
define('MSG_WRONG_PARAMS', '参数错误！');



function p($array)
{
	dump($array,1,'<pre>',0);
}


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
		'create_by_id' => session('user_id'),
		'update_by_id' => session('user_id'),
	);
		
}


function generatekey()
{
		return uniqid('',TRUE);
}


function gettoplocation(){
    $Location = M('MasterLocation');
    return $Location->where(array('active' => 0 , 'parent_code' => array('exp','is NULL')))->order('code')->select();
}


function getlocationchildren($id){
    
}


function authcheck($rule,$uid,$relation='or'){
    
    $auth=new Auth();	
	$type = C('AUTH_CONFIG.AUTH_TYPE');
    return $auth->check($rule,$uid,$type,'url',$relation);
 }


function has_all_rules($rule){
    return authcheck($rule,session('user_id'),'and');
}


function has_any_rules(){
    return authcheck($rule,session('user_id'),'or');
}

function in_all_groups(){
    
}

function in_any_groups(){
    
}


function download_file($file){
    if(is_file($file)){
        $length = filesize($file);
        $type = mime_content_type($file);
        $showname =  ltrim(strrchr($file,'/'),'/');
        header("Content-Description: File Transfer");
        header('Content-type: ' . $type);
        header('Content-Length:' . $length);
         if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
             header('Content-Disposition: attachment; filename="' . rawurlencode($showname) . '"');
         } else {
             header('Content-Disposition: attachment; filename="' . $showname . '"');
         }
         readfile($file);
         exit;
     } else {
         exit('文件已被删除！');
     }
}

?>

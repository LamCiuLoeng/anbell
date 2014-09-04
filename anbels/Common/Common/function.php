<?php
require_once 'PHPExcel/IOFactory.php';
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
define('FLAG_WRONG_PARAMS', 5);
define('MSG_WRONG_PARAMS', '参数错误！');
define('FLAG_WRONG_PW', 6);
define('MSG_WRONG_PW', '密码错误！');
define('FLAG_EMPTY_PW', 7);
define('MSG_EMPTY_PW', '请输入密码！');
define('FLAG_EMPTY_ACCOUNT', 8);
define('MSG_EMPTY_ACCOUNT', '请输入账号！');



function p($array)
{
	dump($array,1,'<pre>',0);
}


function login_check($system_no,$password){
	$m['system_no'] = $system_no;
	if( !$m['system_no'] || is_null($m['system_no']) )
	{
		return array("flag" => FLAG_EMPTY_ACCOUNT , "msg" => MSG_EMPTY_ACCOUNT);
	}
	$m['active'] = 0;
	$User = M("AuthUser");
	$user = $User->where($m)->find();
	if(!$user || is_null($user)){
		return array("flag" => FLAG_NOT_EXIST , "msg" => MSG_NOT_EXIST);
	}	
	if(!$password || is_null($password)){
		return array("flag" => FLAG_EMPTY_PW, "msg" => MSG_EMPTY_PW);
	}
	$dbpw = $user['password'];
	// $hashpw = crypt($password,$user['salt']);
	// $hashpw = authcode($password);
	
	$hashpw = authcode($dbpw,$operation = 'DECODE');
	
	if($password != $hashpw){
		//return array("flag" => FLAG_WRONG_PARAMS, "msg" => MSG_WRONG_PARAMS);
		return array("flag" => FLAG_WRONG_PW, "msg" => MSG_WRONG_PW);
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
		'update_by_id' => session('user_id')
	);
		
}

function mydto_edit(){
	return array(
		'update_time' => mynow(),
		'active' => 0,
		'update_by_id' => session('user_id')
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


function has_any_rules($rule){
    return authcheck($rule,session('user_id'),'or');
}

function in_all_groups($groups){
}

function in_any_groups($groups,$user_id){
    $sql = "select g.*
                from anbels_auth_group_access gu ,anbels_auth_group g, anbels_auth_user u
                where g.id = gu.group_id and u.id = uid and g.title in ('".$groups."') and u.id=".$user_id;
    $q = M()->query($sql);
    if(!$q || is_null($q)){ return FALSE; }
    else{ return TRUE;}       
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

function readXLS($input_file){
    $objPHPExcel = PHPExcel_IOFactory::load($input_file);
    $sheetData = $objPHPExcel->getSheet(0)->toArray(null, true, true, true);
    return $sheetData;
}

function generatepw( $length = 8 ,$chars = null ) {
    // 密码字符集，可任意添加你需要的字符
    if(is_null($chars)){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
    }
    $password = '';
    for ( $i = 0; $i < $length; $i++ ) 
    {
       $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }

    return $password;
}


function getUserSchool($user_id){
    $s = M()->query("
        select s.*
        from anbels_master_school s, anbels_master_class c ,anbels_logic_class_user cu
        where s.id = c.school_id and c.id = cu.class_id and cu.user_id = ".$user_id."  group by s.id");
    if($s){
        return $s[0];
    }else{
        return null;
    }
}




function authcode($string, $operation = 'ENCODE', $key = '', $expiry = 0) {  
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙  
    $ckey_length = 4;  
       
    // 密匙  
    $key = md5($key ? $key : C('ENCRYPT_KEY'));
       
    // 密匙a会参与加解密  
    $keya = md5(substr($key, 0, 16));  
    // 密匙b会用来做数据完整性验证  
    $keyb = md5(substr($key, 16, 16));  
    // 密匙c用于变化生成的密文  
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';  
    // 参与运算的密匙  
    $cryptkey = $keya.md5($keya.$keyc);  
    $key_length = strlen($cryptkey);  
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性  
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确  
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;  
    $string_length = strlen($string);  
    $result = '';  
    $box = range(0, 255);  
    $rndkey = array();  
    // 产生密匙簿  
    for($i = 0; $i <= 255; $i++) {  
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);  
    }  
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度  
    for($j = $i = 0; $i < 256; $i++) {  
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;  
        $tmp = $box[$i];  
        $box[$i] = $box[$j];  
        $box[$j] = $tmp;  
    }  
    // 核心加解密部分  
    for($a = $j = $i = 0; $i < $string_length; $i++) {  
        $a = ($a + 1) % 256;  
        $j = ($j + $box[$a]) % 256;  
        $tmp = $box[$a];  
        $box[$a] = $box[$j];  
        $box[$j] = $tmp;  
        // 从密匙簿得出密匙进行异或，再转成字符  
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));  
    }  
    if($operation == 'DECODE') {  
        // substr($result, 0, 10) == 0 验证数据有效性  
        // substr($result, 0, 10) - time() > 0 验证数据有效性  
        // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性  
        // 验证数据有效性，请看未加密明文的格式  
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {  
            return substr($result, 26);  
        } else {
            return '';  
        }  
    } else {  
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因  
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码  
        return $keyc.str_replace('=', '', base64_encode($result));  
    }  
}


?>

<?php

function qu($location_id)
{	
$master_location = M("master_location"); // 实例化User对象
$data = $master_location->where('id='.$location_id)->find();
return $data[name];
}

function shi($location_id)
{	
$master_location = M("master_location"); // 实例化User对象
$data_qu = $master_location->where('id='.$location_id)->find();
$data_shi= $master_location->where('code='.$data_qu[parent_code])->find();
return $data_shi[name];
}

function sheng($location_id)
{	
$master_location = M("master_location"); // 实例化User对象
$data_qu = $master_location->where('id='.$location_id)->find();
$data_shi= $master_location->where('code='.$data_qu[parent_code])->find();
$data_sheng= $master_location->where('code='.$data_shi[parent_code])->find();
return $data_sheng[name];
}

function xuexiao($school_id)
{	
$master_school = M("master_school"); // 实例化User对象
$data = $master_school->where('id='.$school_id)->find();
return $data[name];
}

function show_edit_user($user_id)
{	
$auth_user = M("auth_user"); // 实例化User对象
$user = $auth_user->where('id='.$user_id)->find();
return $user[name];
}


?>



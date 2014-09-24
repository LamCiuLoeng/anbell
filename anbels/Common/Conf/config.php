<?php
return array(
    //'配置项'=>'配置值'
     'db_type'  => 'mysql',
     'db_host'  => 'localhost',
     'db_port'  => '3306',
     'db_name'  => 'anbels',
     'db_user'  => 'root',
     'db_pwd'  => '',
     'DB_CHARSET'=> 'utf8',
     
     'DB_PREFIX' => 'anbels_',
	 
	 
     'TMPL_TEMPLATE_SUFFIX' => '.html',
	 'URL_HTML_SUFFIX' => '',
	 'URL_MODEL'=> 2 ,
	 
	 //默认错误跳转对应的模板文件
	 'TMPL_ACTION_ERROR' => 'Public:error',
	 //默认成功跳转对应的模板文件
	 'TMPL_ACTION_SUCCESS' => 'Public:success',
	 
	 'ENCRYPT_KEY' => 'dingnigefei',
);
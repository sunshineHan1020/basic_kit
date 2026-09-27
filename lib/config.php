<?php

	$_cfg['db_host'] = 'localhost';//'172.27.0.248'; // 211.253.27.6
	$_cfg['db_user'] = 'root'; 
	$_cfg['db_password'] = '';
	$_cfg['db_database'] = 'db_vona';
	$_cfg['dsn']=array("mysql:host=".$_cfg['db_host'].";dbname=".$_cfg['db_database'],$_cfg['db_user'],$_cfg['db_password']);


	//$_cfg['http']='http://';
	//$_cfg['domain']='211.253.27.6';

?>
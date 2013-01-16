<?php
$_CPHP = true;
$_CPHP_CONFIG = "../config.json";
require("cphp/base.php");

$_APP = true;

function __autoload($class_name) 
{
	global $_APP;
	
	$class_name = str_replace("\\", "/", strtolower($class_name));
	require_once("classes/{$class_name}.php");
}

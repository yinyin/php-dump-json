<?php

$unit = trim( isset($_POST['unit']) ? $_POST['unit'] : $_GET['unit'] );
$callback = trim($_GET['callback']);

define('BASEPATH', '/tmp');
require_once('../json_helper.php');

if('special-char' == $unit)
{
	$d = array('str1' => "abc", 'str2' => "\\\'\"&\n\r<>", 'str3' => "\ta\nb");
	dump_json($d, true, true);
}



// vim: ts=4 sw=4 ai nowrap

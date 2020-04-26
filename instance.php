<?php
namespace DataXscript;
require_once("vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/data-xscript/DataXscript.php");
$obj_data=new DataXscript();
$obj_data->fn_execute();
?>

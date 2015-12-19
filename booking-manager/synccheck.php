<?php 
include("application.php");
include("db.class.php");
include("functions.php");
include("esmtp.class.php");

$vrt = db::runQuery("select (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(created)) as created from |log| where message = 'Finished.' and type = 'sync' order by ID desc",0,1);
//$vrtest = db::runQuery("select ID, type, message, created from |log| where message = 'Finished.' and type = 'sync' order by created desc",0,100);
//var_dump($vrt);
//var_dump($vrtest);
if($vrt[0]['created']>=3630){
	die($vrt[0]['created']." Seconds");
} else {
	die("OK");
}

?>A
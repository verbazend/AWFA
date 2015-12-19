<?php
include("application.php");
include("db.class.php");
include("functions.php");
include("esmtp.class.php");

//var_dump($_GET);
$suburbName = db::esc($_GET['term']);
//$ret = db::runQuery("select suburbName, postcode_db.* from |startrack_suburbs| left join postcode_db on postcode_db.postcode = startrack_suburbs.postcode and postcode_db.suburb = startrack_suburbs.suburbName where countryCode = 'AU' and suburbName like '$suburbName%'");
$ret = db::runQuery("select suburbName, startrack_suburbs.postcode, postcode_db.state from |startrack_suburbs| left join postcode_db on startrack_suburbs.postcode like CONCAT('%', postcode_db.postcode ,'%') where countryCode = 'AU' and suburbName like '$suburbName%' group by suburbName");
$suburbArr = array();
if($ret){
	foreach($ret as $retsb => $retsbv){
		if($retsbv['postcode']==""){
			$suburbArr = array_merge($suburbArr,array(array('value'=>$retsbv['suburbName'],'label'=>$retsbv['suburbName']." ".$retsbv['state']." ".$retsbv['postcode'])));
		} else {
			$pcode = $retsbv['postcode'];
			if($pcode=="4000"){
				$pcode = "4001";
			} 
			$suburbname = $retsbv['suburbName'];
			$suburbArr = array_merge($suburbArr,array(array('value'=>$suburbname."|".$retsbv['state']."|".$pcode,'label'=>$retsbv['suburbName']." ".$retsbv['state']." ".$retsbv['postcode'])));
		}
				

	}
	$retv = $suburbArr;
} else {
	$retv = false;
}

echo json_encode($retv);			
				?>
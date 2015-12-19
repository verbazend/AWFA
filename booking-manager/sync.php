<?php
include("application.php");
include("db.class.php");
include("functions.php");
include("esmtp.class.php");
	
	
	//$locationCache = array();
	//$locationCache = array();
	
	$locationTotal = 0;
	$eventTotal = 0;
	$locationMerge = 0;
	$closedTotal = 0;
	
	
	dlog("Starting","sync");

	$url = axcelerate_get_url().'/api/courses?displayLength=100&type=w';
	//$url = axcelerate_get_url().'/api/courses';
	$courses = getAXData($url);
	
	//var_dump($courses);

	$new_course = 0;

	//db::insertQuery("update courses set  deleted = '1'");
	
	
	dlog("Update all courses to deleted","sync");
	
	foreach ( $courses as $course ) {
	    	
			$courseID   = db::esc($course->ID); 
			$courseName = db::esc($course->NAME);
			$courseCode = db::esc($course->CODE);
			$courseType = db::esc($course->TYPE);
			
			$hascourse = db::runQuery("select count(*) as total from courses where courseID = '".$courseID."'");
			//var_dump($hascourse);
			
			if($hascourse[0]['total']==0){
				db::insertQuery("insert into courses (CourseID, CourseName, websiteName, Code, Type, deleted) values('".$courseID."','".$courseName."','".$courseName."','".$courseCode."','".$courseType."','0')");
				golog("Created $courseID");
				dlog("Created $courseID Type W","sync");
			} else {
				$tdata = db::runQuery("select * from courses where courseID = '".$courseID."'");
				$tdata = $tdata[0];
				if($tdata['websiteName']==""){
					$websitename = $courseName;
				} else {
					$websitename = $tdata['websiteName'];
				}
				db::insertQuery("update courses set CourseName = '".$courseName."', websiteName = '".$websitename."', Code = '".$courseCode."', Type = '".$courseType."', deleted = '0' where CourseID = '".$courseID."'");
				golog("Updated $courseID");
				//dlog("Updated $courseID Type W","sync");
			}
			$locationTotal++;
	}
	
	$url = axcelerate_get_url().'/api/courses?displayLength=100&type=p';
	//$url = axcelerate_get_url().'/api/courses';
	$courses = getAXData($url);
	
//var_dump($courses);
	$new_course = 0;


	//This one is not needed as we run it above
	//db::insertQuery("update courses set  deleted = '1'");
	
	foreach ( $courses as $course ) {
	    	
			$courseID   = db::esc($course->ID); 
			$courseName = db::esc($course->NAME);
			$courseCode = db::esc($course->CODE);
			$courseType = db::esc($course->TYPE);
			
			$hascourse = db::runQuery("select count(*) as total from courses where courseID = '".$courseID."'");
			//var_dump($hascourse);
			if($hascourse[0]['total']==0){
				db::insertQuery("insert into courses (CourseID, CourseName, websiteName, Code, Type, deleted) values('".$courseID."','".$courseName."','".$courseName."','".$courseCode."','".$courseType."','0')");
				golog("Created $courseID");
				dlog("Created $courseID Type P","sync");
			} else {
				$tdata = db::runQuery("select * from courses where courseID = '".$courseID."'");
				$tdata = $tdata[0];
				if($tdata['websiteName']==""){
					$websitename = $courseName;
				} else {
					$websitename = $tdata['websiteName'];
				}
				db::insertQuery("update courses set CourseName = '".$courseName."', websiteName = '".$websitename."', Code = '".$courseCode."', Type = '".$courseType."', deleted = '0' where CourseID = '".$courseID."'");
				golog("Updated $courseID");
				//dlog("Updated $courseID Type P","sync");
			}
			$locationTotal++;
	}
	
	$coursedata = db::runQuery("select * from courses");
	
	db::insertQuery("update events set enrolmentOpen = '0', processed = '0', active = '0'");
	golog("Set all events to processing and non active");
	dlog("Marking all events as not open, not processed and not active.","sync");
		
	foreach($coursedata as $course){
		$courseType = $course['Type'];
		$courseID = intval($course['CourseID']);
		$url = axcelerate_get_url()."/api/course/instances?type=".$courseType."&id=".$courseID;
		$courses = getAXData($url);
		
		if($courseID=="9137"){
			//var_dump($url);	
			//var_dump($courses);
		}
		
	
		
		foreach($courses as $singlecourse){
				
			
			
			$courseID                 = db::esc($singlecourse->ID);
			$instanceID               = db::esc($singlecourse->INSTANCEID);
			$locationDataName		  = db::esc($singlecourse->LOCATION);
			$startDateTime            = db::esc($singlecourse->STARTDATE);
			$endDateTime              = db::esc($singlecourse->FINISHDATE);
			$courseData               = date("Y-m-d",strtotime("".$startDateTime."")); //NEDS TO BE DATE OF START DATE
			$cost                     = db::esc($singlecourse->COST);
			$maxparticipants          = db::esc($singlecourse->MAXPARTICIPANTS);
			$totalParticipants        = db::esc($singlecourse->PARTICIPANTS);
			$totalParticipantsVacancy = db::esc($singlecourse->PARTICIPANTVACANCY);
			$enrolmentOpen            = db::esc($singlecourse->ENROLMENTOPEN);
			
			golog("Syncing $instanceID. EO: $enrolmentOpen");
			
			//echo $enrolmentOpen."<br>";
			
			if($totalParticipantsVacancy<=0){
				$enrolmentOpen = 0;
				//echo($instanceID);
				//echo("<br>");
			}
			
			$locationID = getLocationIDfromDataName($locationDataName);
			
			$instance = db::runQuery("select * from events where instanceID = '$instanceID'");
			if($instance){
				
				if($enrolmentOpen==1){
					db::insertQuery("update events set courseID = '$courseID', locationID = '$locationID', startDateTime = '$startDateTime', endDateTime = '$endDateTime', courseDate = '$courseData', cost = '$cost', maxparticipants = '$maxparticipants', totalParticipants = '$totalParticipants', totalParticipantsVacancy = '$totalParticipantsVacancy', enrolmentOpen = '$enrolmentOpen', updated = now(), processed = '1', active = '1' where instanceID = '$instanceID'");				
				} else {
					db::insertQuery("update events set courseID = '$courseID', locationID = '$locationID', startDateTime = '$startDateTime', endDateTime = '$endDateTime', courseDate = '$courseData', cost = '$cost', maxparticipants = '$maxparticipants', totalParticipants = '$totalParticipants', totalParticipantsVacancy = '$totalParticipantsVacancy', enrolmentOpen = '$enrolmentOpen', updated = now(), processed = '1', active = '0' where instanceID = '$instanceID'");
					if(!$instance['enrolmentOpen']==$enrolmentOpen){
						db::insertQuery("insert into log (type,message,instanceID) values('sync','Istance Enrolment Closed','$instanceID')");
						$closedTotal++;
					}
				}
				
			} else {
				if($enrolmentOpen){
					db::insertQuery("insert into events (instanceID, courseID, locationID, startDateTime, endDateTime, courseDate, cost, maxparticipants, totalParticipants, totalParticipantsVacancy, enrolmentOpen, updated, processed, active) values('$instanceID','$courseID','$locationID','$startDateTime','$endDateTime','$courseData','$cost','$maxparticipants','$totalParticipants','$totalParticipantsVacancy','$enrolmentOpen',now(),'1','1')");
					db::insertQuery("insert into log (type,message,instanceID) values('sync','Istance Created','$instanceID')");
				}	
			}
			$eventTotal++;
			
		}
		
		
		
		//var_dump($courses);
		//die();
	}
	
	$mergedata = db::runQuery("select * from locations where mergeWithID != '0'");
	if($mergedata){
		foreach($mergedata as $merge){
			$oldLocationID = db::esc($merge['ID']);
			$newLocationID = db::esc($merge['mergeWithID']);
			db::insertQuery("update events set locationID = '$newLocationID' where locationID = '$oldLocationID'");
			$locationMerge++;
		}
	}
	
	dlog("Finalising","sync");
	
	echo "Total Locations Processed: ".$locationTotal."<br>\n";
	echo "Total Locations Merged: ".$locationMerge."<br>\n";
	echo "Total Events Processed: ".$eventTotal."<br>\n";
	echo "Total Events Closed: ".$closedTotal."<br>\n";
	echo "Completed at: ".date("Y-m-d g:i:s A",strtotime("now"));
	
	dlog("############################################","sync");
	dlog("# Total Locations Processed: $locationTotal","sync");
	dlog("# Total Locations Merged: $locationMerge","sync");
	dlog("# Total Events Processed: $eventTotal","sync");
	dlog("# Total Events Closed: $closedTotal","sync");
	dlog("############################################","sync");
	
	dlog("Finished.","sync");
?>
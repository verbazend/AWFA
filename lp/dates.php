<?php
if (empty($_POST['locations_id'])) {
	//exit;
}

$location_id = $_POST['locations_id'];
if( ! isset($_POST['courses_id']) || $_POST['courses_id'] == NULL || $_POST['courses_id'] == '' || $_POST['courses_id'] == 0) {
	$course_id = 12;
} else {
	$course_id = $_POST['courses_id'];
}
$date = date('Y-m-d');

if($location_id != null) {
	if($course_id == null || $course_id == '') {
		$course_id=12;
	}
	$objDB = new PDO('mysql:host=223.27.21.211;dbname=newadmin_booking', 'newtestadmin', 'testpwd');
	$objDB->exec('SET CHARACTER SET utf8');
	$sql = 'SELECT * FROM course_dates INNER JOIN course_dates_courses ON course_dates_courses.course_date_id=course_dates.id WHERE locations_id = '.$location_id.' AND course_dates_courses.course_id = '.$course_id.' AND date > '.$date.' AND (companies_id = 0 OR companies_id IS NULL) AND course_dates.deleted = 0 ORDER BY date ASC';
	$query = $objDB->query($sql);
	//$date = new DateTime();
	$dates = array();;
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		//$tempdate = explode("-", $row['date']);
		//$date = mktime(0, 0, 0, $old_date[0], $old_date[1], $old_date[2]);
		if($row['date'] > $date) {
			$dates[] = $row['date'];
		}
		
		
		
		/*$old_date = explode('-', $row['date']);
		$date->setDate($old_date[0], $old_date[1], $old_date[2]);
		$dates[] = $date->format('Y-m-d');*/
	}
}


echo json_encode(array('dates' => $dates));//, JSON_NUMERIC_CHECK);
?>
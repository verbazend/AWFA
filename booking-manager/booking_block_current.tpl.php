<?php
if(isset($_SESSION['axcelerate']['courseid'])){
  if(is_numeric($_SESSION['axcelerate']['courseid'])){
    $node = node_load($_SESSION['axcelerate']['courseid']);
   // $iNode = node_load($node->field_course_instance_course['und'][0]['nid']);
    $_SESSION['axcelerate']['course_instance'] = $node->field_instance_id['und'][0]['value'];
    $location = taxonomy_term_load($node->field_location['und'][0]['tid']);
    //print_r($location);
   ?> 
    <h3>Your Selected Course</h3>
    <ul>
        <li><span>Course:</span> <?php print $node->title ?></li>
        <li><span>Course Date:</span> <?php print date('d M Y',strtotime($node->field_course_instance_start_date['und'][0]['value']))?></li>
        <li><span>Course Timings:</span> <?php print date('h:i a',strtotime($node->field_course_instance_start_date['und'][0]['value']))?> - <?php print date('h:i a',strtotime($node->field_course_instance_finish_dat['und'][0]['value']))?></li>
        <li><span>Location:</span> <?php print $location->name; ?></li>
        <li><span>Total Cost:</span>$ <?php print number_format($node->field_cost['und'][0]['value'] ,2)?></li>
    </ul>
        
    <?php
  }
    
}

?>
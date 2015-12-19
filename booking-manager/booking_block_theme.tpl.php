<div id="axcelerateBookingFormBloock">
<?php
$path = drupal_get_path('module','axcelerate');
drupal_add_library('system', 'ui.datepicker');
//print $path;
drupal_add_js($path.'/js/axcelerate.js');
$form = drupal_get_form('axcelerate_booking_form_form');
print drupal_render($form);
?>
</div>

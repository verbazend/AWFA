<?php

//print_r($form);

//print_r($_SESSION['axcelerate']);

switch($step){
    case '1':
        ?>
        <h2>Enter Your Details:</h2>
Please enter your name exactly as you want it displayed on your certificate.
        <?php
        print drupal_render($form);
        break;
        case '2':
                    ?>
        <h2>Verify Your Personal Details:</h2>
Please ensure ALL your information is correct before you confirm your booking and proceed to the payment page.
        <?php
        print drupal_render($form);
        break;
    case '3':
$path = drupal_get_path('module','axcelerate');
drupal_add_js($path.'/js/axcelerate.js');
                            ?>
        <h2>Select Payment Method:</h2>
Your booking has been confirmed, you should be receiving a confirmation email soon. Please proceed further by selecting a payment method.
        <?php
        
        print drupal_render($form);
        break;
        case '4':
          drupal_set_title('Confirm');
          ?>
            <h2>Contact Details</h2>
            <table>
                <tr>
                    <td>First Name</td>
                    <td><?php print $_SESSION['axcelerate']['fname']; ?></td>
                    
                </tr>
                
                                <tr>
                    <td>Last Name</td>
                   <td><?php print $_SESSION['axcelerate']['lname']; ?></td>
                    
                </tr>
                                
                                                <tr>
                    <td>Mobile Number</td>
                   <td><?php print $_SESSION['axcelerate']['mobile']; ?></td>
                    
                </tr>
                                                
                <tr>
                    <td>Email</td>
                    <td><?php print $_SESSION['axcelerate']['email']; ?></td>
                    
                </tr>
                
                                <tr>
                    <td>Workplace</td>
                    <td><?php print $_SESSION['axcelerate']['workplace']; ?></td>
                    
                </tr>
                                
                                                <tr>
                    <td>Address</td>
                  <td><?php print $_SESSION['axcelerate']['address']; ?></td>
                    
                </tr>
                                                
                                                                <tr>
                    <td>Suburb</td>
                   <td><?php print $_SESSION['axcelerate']['suburb']; ?></td>
                    
                </tr>
                                                                
                                                                                <tr>
                    <td>Postcode</td>
                    <td><?php print $_SESSION['axcelerate']['postcode']; ?></td>
                    
                </tr>
                
            </table>
             <h2>Payment Details</h2>
            <?php
            
            switch($_SESSION['axcelerate']['payment']){
                case '1':
                    ?>
            <table>
                
                                <tr>
                    <td>Method</td>
                    <td>Credit Card</td>
                    
                </tr>
                
                <tr>
                    <td>Name On Card</td>
                    <td><?php print $_SESSION['axcelerate']['name']; ?></td>
                    
                </tr>
                
                                <tr>
                    <td>CC Number</td>
                   <td><?php print $_SESSION['axcelerate']['cc']; ?></td>
                    
                </tr>
                                
                                                                <tr>
                    <td>Expiry</td>
                   <td><?php print $_SESSION['axcelerate']['expiryM']; ?> / <?php print $_SESSION['axcelerate']['expiryY']; ?></td>
                </tr>
                                                                
                                                                        <tr>
                    <td>CVV</td>
                   <td><?php print $_SESSION['axcelerate']['cvv']; ?></td>
                </tr>
                                
                                
            </table>  
                    
                    <?php
                    break;
                case 2:
                    ?>
                               <table>
                
                                <tr>
                    <td>Method</td>
                    <td>Invoice</td>
                    
                </tr> 
                    </table>
                 <?php   
                    break;
                  }
                   print drupal_render($form);
                  break;
               
                case 5:
                     print drupal_render($form);
                   //  $_SESSION['axcelerate'] = array();
                    ?>
                    
                   <?php
                    break;
          
            
        print drupal_render($form);
}

?>
<?php
class email  {


			
	public function __construct()
    {
        //var_dump(date("Y"));
		//die();
		
		//DB Configuration
		
		
		
    }
    
    
    function loademailtemplate($templatename){
                return file_get_contents(APP_PATH."/email_templates/".$templatename.".php");
    }
    
    function sendemail($remail, $cc, $bcc, $subject, $message, $attachments, $listingID = 0)  {
            
            $semail = db::getGlobalSetting('defaultemailsender');
            $sname = db::getGlobalSetting('defaultemailsendername');
            
            //$bcc = "andrew@mattersolutions.com.au";
            
			//$remail = $bcc;
			
            $rname = "";
            $priority = "high";
            $type = "";
            $replysemail = db::getGlobalSetting('defaultemailsenderreplytoemail');
			
			
            
            // Checks if carbon copy & blind carbon copy exist
            if($cc != null){$cc="CC: ".$cc."\r\n";}else{$cc="";}
            if($bcc != null){$bcc="BCC: ".$bcc."\r\n";}else{$bcc="";}
            
            // Checks the importance of the email
            if($priority == "high"){$priority = "X-Priority: 1\r\nX-MSMail-Priority: High\r\nImportance: High\r\n";}
            elseif($priority == "low"){$priority = "X-Priority: 3\r\nX-MSMail-Priority: Low\r\nImportance: Low\r\n";}
            else{$priority = "";}
            
            // Checks if it is plain text or HTML
            if($type == "plain"){$type="text/plain";}else{$type="text/html";}
            
            // The boundary is set up to separate the segments of the MIME email
            $boundary = md5(@date("Y-m-d-g:ia"));
            
            // The header includes most of the message details, such as from, cc, bcc, priority etc. 
            $header = "From: ".$sname." <".$semail.">\r\nMIME-Version: 1.0\r\nX-Mailer: PHP\r\nReply-To: ".$sname." <".$replysemail.">\r\nReturn-Path: ".$sname." <".$replysemail.">\r\n".$cc.$bcc.$priority."Content-Type: multipart/mixed; boundary = ".$boundary."\r\n\r\n";    
              
            // The full message takes the message and turns it into base 64, this basically makes it readable at the recipients end
            $fullmessage .= "--".$boundary."\r\nContent-Type: ".$type."; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n".chunk_split(base64_encode($message));
            
            // A loop is set up for the attachments to be included.
            if($attachments != null) {
              foreach ($attachments as $attachment)  {
                $attachment = explode(":", $attachment);
                $fullmessage .= "--".$boundary."\r\nContent-Type: ".$attachment[1]."; name=\"".$attachment[2]."\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment\r\n\r\n".chunk_split(base64_encode(file_get_contents($attachment[0])));
              }
            }
            
            // And finally the end boundary to set the end of the message
            $fullmessage .= "--".$boundary."--";
			
			
			self::logemail($remail, $cc, $bcc, $subject, $message, $listingID, $_SESSION['userid']); 
			esmtp::sendemail_smtp($remail,$subject,$message);
            //echo($fullmessage);
            return true; //mail($rname."<".$remail.">", $subject, $fullmessage, $header);
			
			
    }

	public function logemail($remail, $cc, $bcc, $subject, $message, $listingID, $userID){
			
			$remail  = db::esc($remail);
			$cc      = db::esc($cc);
			$bcc     = db::esc($bcc);
			$subject = db::esc($subject);
			$message = db::esc($message);
			
			db::insertQuery("insert into |email_history| (subject, emailhtml, listingID, userID, toaddress, ccaddress, bccaddress) values('".$subject."','".$message."','".$listingID."','".$userID."','".$remail."','".$cc."','".$bcc."')");
			
			return true;
	
	}
	
	public function encode_email($e) {
		for ($i = 0; $i < strlen($e); $i++) { $output .= '&#'.ord($e[$i]).';'; }
		return $output;
	}
	
    
    public function getreferaltemplateforemail($listingID, $tojanssen = false){
        
            $patientdata = patient::getpatientbyID($listingID);
            $patientdata = $patientdata[0];
            
            $userdata = Login::getuser($patientdata['userID']);
            if($userdata['practtype']==1){ $practtype = "Doctor"; } else { $practtype = "Nurse"; }
            
            $emailtemplate = '
            <h1>Practitioners Details</h1>
            <p>Details about the practitioner who submitted the referal.<br>If Serious or Life Threatening please contact them directly as well.</p>
                <div><label class="label"><span class="bold">Practitioner:</span> <span>'.$userdata['fullname'].'</span></label></div>
                <div><label class="label"><span class="bold">Institution:</span> <span>'.$userdata['institution'].'</span></label></div>
                <br>
                <div><label class="label"><span class="bold">Practitioner Type:</span> <span>'.$practtype.'</span></label></div>
                <div><label class="label"><span class="bold">AHPRA Registration No.:</span> <span>'.$userdata['ahpraregnumber'].'</span></label></div>
                <br>
                <div><label class="label"><span class="bold">State:</span> <span>'.$userdata['state'].'</span></label></div>
                <div><label class="label"><span class="bold">Postcode:</span> <span>'.$userdata['postcode'].'</span></label></div>
                <div><label class="label"><span class="bold">Direct Phone:</span> <span>'.$userdata['directphone'].'</span></label></div>
                <div><label class="label"><span class="bold">Mobile (Emergency Only):</span> <span>'.$userdata['mobile'].'</span></label></div>
                <div><label class="label"><span class="bold">After Hours Phone:</span> <span>'.$userdata['afterhoursph'].'</span></label></div>
                
                <br>
             
            <h1>Patient Details</h1>
            
            <div>
                <div><label class="label"><span class="bold">My patient has signed the consent form: </span> <span>YES</span></label></div>
                <div><label class="label"><span class="bold">I have faxed a copy of the patient’s Consent Form to Central Brisbane Dermatology on (07) 3831 4387: </span> <span>YES</span></label></div>
                <br>
                <div><label class="label"><span class="bold">Patient ID:</span> <span>TESA'.$patientdata['ID'].'</span></label></div>';
                if($tojanssen){
                	$emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Patient\'s First Name:</span> <span>'.substr($patientdata['firstname'],0,1).'</span></label></div>';
                	$emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Patient\'s Last Name:</span> <span>'.substr($patientdata['lastname'],0,1).'</span></label></div>';
                } else {
                	$emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Patient\'s First Name:</span> <span>'.$patientdata['firstname'].'</span></label></div>';
                	$emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Patient\'s Last Name:</span> <span>'.$patientdata['lastname'].'</span></label></div>';
                }
                
                $emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Institution:</span> <span>'.$patientdata['instituation'].'</span></label></div>
                <div><label class="label"><span class="bold">Postcode of Clinic/Institution where patient will be treated*:</span> <span>'.$patientdata['postcode'].'</span></label></div>
                <div><label class="label"><span class="bold">DOB:</span> <span>'.$patientdata['DOB'].'</span></label></div>
                <div><div class="label"><span class="bold">Sex:</span> <span>'.$patientdata['sex'].'</span> </div></div>
                <div><label class="label"><span class="bold">Date of onset of current skin problem (if known):</span> <span>'.$patientdata['dateonset'].'</span></label></div>
                <div><label class="label"><span class="bold">History of current condition:</span> <span>'.$patientdata['historyofcondition'].'</span></label></div>
                
                
                
                
                <div><div class="label"><span class="bold">Does your patient have any of the following conditions (Tick all that apply)?:</span> 
                    <span>';
                     
                    
                    $conditions = db::runQuery('select * from |conditions| where conditiongroup = 1',0,99);
                    
                    foreach($conditions as $item){
                        $isselected = functions::patienthasconditionbyid($patientdata['ID'],$item['ID']);
                        //e($isselected);
                        if($isselected){
                            
                            $emailtemplate =  $emailtemplate . $item['ConditionName']."<br>";
                        }
                    }
                    
                $emailtemplate = $emailtemplate.'</span>
                </div></div>
                <div><label class="label"><span class="bold">Other associated symptoms:</span> <span>'.$patientdata['otherassociatedsymptopms'].'</span></label></div>
                
                <div><label class="label"><span class="bold">Progress notes:</span> <span>'.$patientdata['progressnotes'].'</span></label></div>
                <div><label class="label"><span class="bold">Current topical therapies:</span> <span>'.$patientdata['currenttopicalthereapies'].'</span></label></div>
                
                <h1 class="formheader">Previous Medical History</h1>
                <div><div class="label"><span class="bold">Has your patient previously experienced the following conditions:</span> 
                    <span>';
                     
                    
                    $conditions = db::runQuery('select * from |conditions| where conditiongroup = 2',0,99);
                    
                    foreach($conditions as $item){
                        
                        $isselected = functions::patienthasconditionbyid($patientdata['ID'],$item['ID']);
                        
                        if($isselected){
                            
                            $emailtemplate = $emailtemplate.$item['ConditionName']."<br>";
                        }
                    }
                    
                    
                $emailtemplate = $emailtemplate.'</span>
                </div></div>
                <div><label class="label"><span class="bold">Known Allergies:</span> <span>'.$patientdata['knownallergies'].'</span></label></div>
                <div><label class="label"><span class="bold">Examination/distribution:</span> <span>'.$patientdata['examinationdistribution'].'</span></label></div>
                
                <h1 class="formheader">Drug History - All Drugs</h1>
                <div><div class="label"><span class="bold">Is your patient currently on telaprevir:</span> 
                    <span>
                    '.$patientdata['ontelaprevir'].'
                    </span>
                </div></div>
                <br>
                <div class="wraptab">
                
                <br>

                    ';
					
    
				    $data = db::runQuery("SELECT * from |patient_druglist| where patientID = '".db::esc($patientdata['ID'])."'");
				    //var_dump($data);
				    if($data){
				    foreach($data as $drug){
				        //echo("loadnewdrugtolist('".$drug['drug']."','".$drug['indication']."','".$drug['datecommenced']."','".$drug['currentdoseandfrequency']."','".$drug['doseadjustmentdates']."');\n");
						
						$emailtemplate = $emailtemplate.'<div class="emaildruglistwrapper">';
						$emailtemplate = $emailtemplate.'<div class="druglistitememail">Drug: <span>'.$drug['drug'].'</span></div>';
                        $emailtemplate = $emailtemplate.'<div class="druglistitememail">Indication: <span>'.$drug['indication'].'</span></div>';
                        $emailtemplate = $emailtemplate.'<div class="druglistitememail">Date commenced: <span>'.$drug['datecommenced'].'</span></div>';
                        $emailtemplate = $emailtemplate.'<div class="druglistitememail">Current dose &amp; frequency: <span>'.$drug['currentdoseandfrequency'].'</span></div>';
                        $emailtemplate = $emailtemplate.'<div class="druglistitememail">Dose adjustment dates: <span>'.$drug['doseadjustmentdates'].'</span></div>';
						$emailtemplate = $emailtemplate.'</div><br><br>';
				    }
				    }
				    
				$emailtemplate = $emailtemplate.'

                </div>
                
                <br>
                <div><label class="label"><span class="bold">Additional relevant history:</span> <span>'.$patientdata['additionaldrughistory'].'</span></label></div>
                
                <div id="fileuploadwrapper">
                    
                </div>
                <br>
                <div id="filelistwrapper">
                    
                </div>
                <div id="response"></div>  
                <ul id="image-list">  
              
                </ul> 
                <br>
                
                <input type="hidden" name="totaldrugs" id="totaldrugs" value="0">
                <input type="hidden" name="totalattachments" id="totalattachments" value="0">
            </div>
            ';
            
            return $emailtemplate;
        
    }

    public function getfollowupreferaltemplateforemail($listingID, $tojanssen = false){
        
            $patientdata = patient::getpatientbyID($listingID);
            $patientdata = $patientdata[0];
            
            $userdata = Login::getuser($patientdata['userID']);
            if($userdata['practtype']==1){ $practtype = "Doctor"; } else { $practtype = "Nurse"; }
            
            $emailtemplate = '
            <h1>Practitioners Details</h1>
            <p>Details about the practitioner who submitted the referal.<br>If Serious or Life Threatening please contact them directly as well.</p>
                <div><label class="label"><span class="bold">Practitioner:</span> <span>'.$userdata['fullname'].'</span></label></div>
                <div><label class="label"><span class="bold">Institution:</span> <span>'.$userdata['institution'].'</span></label></div>
                <br>
                <div><label class="label"><span class="bold">Practitioner Type:</span> <span>'.$practtype.'</span></label></div>
                <div><label class="label"><span class="bold">AHPRA Registration No.:</span> <span>'.$userdata['ahpraregnumber'].'</span></label></div>
                <br>
                <div><label class="label"><span class="bold">State:</span> <span>'.$userdata['state'].'</span></label></div>
                <div><label class="label"><span class="bold">Postcode:</span> <span>'.$userdata['postcode'].'</span></label></div>
                <div><label class="label"><span class="bold">Direct Phone:</span> <span>'.$userdata['directphone'].'</span></label></div>
                <div><label class="label"><span class="bold">Mobile (Emergency Only):</span> <span>'.$userdata['mobile'].'</span></label></div>
                <div><label class="label"><span class="bold">After Hours Phone:</span> <span>'.$userdata['afterhoursph'].'</span></label></div>
                
                <br>
             
            <h1>Patient Details</h1>
            
            <div>
                <div><label class="label"><span class="bold">My patient has signed the consent form: </span> <span>YES</span></label></div>
                <div><label class="label"><span class="bold">I have faxed a copy of the patient’s Consent Form to Central Brisbane Dermatology on (07) 3831 4387: </span> <span>YES</span></label></div>
                <br>
                <div><label class="label"><span class="bold">Patient ID:</span> <span>TESA'.$patientdata['ID'].'</span></label></div>';
                if($tojanssen){
                	$emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Patient\'s First Name:</span> <span>'.substr($patientdata['firstname'],0,1).'</span></label></div>';
                	$emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Patient\'s Last Name:</span> <span>'.substr($patientdata['lastname'],0,1).'</span></label></div>';
                } else {
                	$emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Patient\'s First Name:</span> <span>'.$patientdata['firstname'].'</span></label></div>';
                	$emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Patient\'s Last Name:</span> <span>'.$patientdata['lastname'].'</span></label></div>';
                }
                $emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Institution:</span> <span>'.$patientdata['instituation'].'</span></label></div>
                <div><label class="label"><span class="bold">Postcode of Clinic/Institution where patient will be treated*:</span> <span>'.$patientdata['postcode'].'</span></label></div>
                <div><label class="label"><span class="bold">DOB:</span> <span>'.$patientdata['DOB'].'</span></label></div>
                <div><div class="label"><span class="bold">Sex:</span> <span>'.$patientdata['sex'].'</span> </div></div>
                <div><label class="label"><span class="bold">Date of onset of current skin problem (if known):</span> <span>'.$patientdata['dateonset'].'</span></label></div>
                <div><label class="label"><span class="bold">History of current condition:</span> <span>'.$patientdata['historyofcondition'].'</span></label></div>
                
                
                
                
                <div><div class="label"><span class="bold">Does your patient have any of the following conditions (Tick all that apply)?:</span> 
                    <span>';
                     
                    
                    $conditions = db::runQuery('select * from |conditions| where conditiongroup = 1',0,99);
                    
                    foreach($conditions as $item){
                        $isselected = functions::patienthasconditionbyid($patientdata['ID'],$item['ID']);
                        //e($isselected);
                        if($isselected){
                            
                            $emailtemplate =  $emailtemplate . $item['ConditionName']."<br>";
                        }
                    }
                    
                $emailtemplate = $emailtemplate.'</span>
                </div></div>
                <div><label class="label"><span class="bold">Other associated symptoms:</span> <span>'.$patientdata['otherassociatedsymptopms'].'</span></label></div>
                
                <div><label class="label"><span class="bold">Progress notes:</span> <span>'.$patientdata['progressnotes'].'</span></label></div>
                <div><label class="label"><span class="bold">Current topical therapies:</span> <span>'.$patientdata['currenttopicalthereapies'].'</span></label></div>
                
                <h1 class="formheader">Previous Medical History</h1>
                <div><div class="label"><span class="bold">Has your patient previously experianced the following conditions:</span> 
                    <span>';
                     
                    
                    $conditions = db::runQuery('select * from |conditions| where conditiongroup = 2',0,99);
                    
                    foreach($conditions as $item){
                        
                        $isselected = functions::patienthasconditionbyid($patientdata['ID'],$item['ID']);
                        
                        if($isselected){
                            
                            $emailtemplate = $emailtemplate.$item['ConditionName']."<br>";
                        }
                    }
                    
                    
                $emailtemplate = $emailtemplate.'</span>
                </div></div>
                <div><label class="label"><span class="bold">Known Allergies:</span> <span>'.$patientdata['knownallergies'].'</span></label></div>
                <div><label class="label"><span class="bold">Examination/distribution:</span> <span>'.$patientdata['examinationdistribution'].'</span></label></div>
                
                <h1 class="formheader">Drug History - All Drugs</h1>
                <div><div class="label"><span class="bold">Is your patient currently on telaprevir:</span> 
                    <span>
                    '.$patientdata['ontelaprevir'].'
                    </span>
                </div></div>
                <br>
                <div class="wraptab">
                
                <br>

                    ';
                    
    
                    $data = db::runQuery("SELECT * from |patient_druglist| where patientID = '".db::esc($patientdata['ID'])."'");
                    //var_dump($data);
                    if($data){
                    foreach($data as $drug){
                        //echo("loadnewdrugtolist('".$drug['drug']."','".$drug['indication']."','".$drug['datecommenced']."','".$drug['currentdoseandfrequency']."','".$drug['doseadjustmentdates']."');\n");
                        
                        $emailtemplate = $emailtemplate.'<div class="druglistitemwrapperemail">';
                        $emailtemplate = $emailtemplate.'<div class="druglistitememail">Drug: <span>'.$drug['drug'].'</span></div>';
                        $emailtemplate = $emailtemplate.'<div class="druglistitememail">Indication: <span>'.$drug['indication'].'</span></div>';
                        $emailtemplate = $emailtemplate.'<div class="druglistitememail">Date commenced: <span>'.$drug['datecommenced'].'</span></div>';
                        $emailtemplate = $emailtemplate.'<div class="druglistitememail">Current dose &amp; frequency: <span>'.$drug['currentdoseandfrequency'].'</span></div>';
                        $emailtemplate = $emailtemplate.'<div class="druglistitememail">Dose adjustment dates: <span>'.$drug['doseadjustmentdates'].'</span></div>';
                        $emailtemplate = $emailtemplate.'</div><br><br>';
                    }
                    }
                    
                $emailtemplate = $emailtemplate.'

                </div>
                
                <br>
                <div><label class="label"><span class="bold">Additional relevant history:</span> <span>'.$patientdata['additionaldrughistory'].'</span></label></div>
                
                <div id="fileuploadwrapper">
                    
                </div>
                <br>
                <div id="filelistwrapper">
                    
                </div>
                <div id="response"></div>  
                <ul id="image-list">  
              
                </ul> 
                <br>
                
                <input type="hidden" name="totaldrugs" id="totaldrugs" value="0">
                <input type="hidden" name="totalattachments" id="totalattachments" value="0">
            </div>
            ';
            
            return $emailtemplate;
        
    }

    public function getresponsereferaltemplateforemail($listingID){
        
            $patientdata = patient::getpatientbyID($listingID);
            $patientdata = $patientdata[0];
            
            $practdata = Login::gettheuser();
            $username = $practdata['email'];
            
            $userdata = Login::getuser($patientdata['userID']);
            if($userdata['practtype']==1){ $practtype = "Doctor"; } else { $practtype = "Nurse"; }
            
            /*
            $emailtemplate = $emailtemplate.'<h1>Practitioners Details</h1>';
            $emailtemplate = $emailtemplate.'<p><b>Details about the practitioner who submitted the referral.<br>If serious or life threatening please contact them directly as well.</b></p>';
            $emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Practitioner:</span> <span>'.$userdata['fullname'].'</span></label></div>';
            $emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Institution:</span> <span>'.$userdata['institution'].'</span></label></div>';
            $emailtemplate = $emailtemplate.'<br>';
            $emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Practitioner Type:</span> <span>'.$practtype.'</span></label></div>';
            $emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">AHPRA Registration No.:</span> <span>'.$userdata['ahpraregnumber'].'</span></label></div>';
            $emailtemplate = $emailtemplate.'<br>';
            $emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">State:</span> <span>'.$userdata['state'].'</span></label></div>';
            $emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Direct Phone:</span> <span>'.$userdata['directphone'].'</span></label></div>';
            $emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">Mobile (Emergency Only):</span> <span>'.$userdata['mobile'].'</span></label></div>';
            $emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold">After Hours Phone:</span> <span>'.$userdata['afterhoursph'].'</span></label></div>';
            $emailtemplate = $emailtemplate.'';
            $emailtemplate = $emailtemplate.'<br>';
            $emailtemplate = $emailtemplate.' ';
             * 
             */
            
            $refresponse = db::runQuery('select * from |patient_responses| where PatientID = '.db::esc($patientdata['ID']).' and active = 1',0,99);

            if($refresponse){
                $refresponse = $refresponse[0];
                $response = db::esc($refresponse['Response']);
                $diagnosisID = db::esc($refresponse['diagnosis_grade']);
                $diagnosisOther = db::esc($refresponse['Diagnosis']);
                $telarelated = db::esc($refresponse['telaprevir_related']);
               
                $followuprecommendation = db::esc($refresponse['followup_recommendation']);
                $consultcomplete = db::esc($refresponse['completed']);
            
                $additionalresponse = db::esc($refresponse['additional_response']);
                
            }

            $emailtemplate = $emailtemplate.'<div class="referralwrapper">';
            $emailtemplate = $emailtemplate.'<h1 class="formheader">Referral Response</h1>';
            $emailtemplate = $emailtemplate.'    ';
            $emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold"><strong>Response:</strong></span> <spane>'.$response.'</spane></label></div>';
            $emailtemplate = $emailtemplate.'<div><div class="label"><span class="bold"><strong>Diagnosis:</strong></span> ';
            $emailtemplate = $emailtemplate.'<span>';
        
            $grade = db::runQuery('select * from |diagnosis_grades| order by ID asc',0,99);
        
            foreach($grade as $item){
                
                    if($diagnosisID==$item['ID']){
                        $checkedoutput = " checked";
                
                        $emailtemplate = $emailtemplate.'<div class="formitem radiobutton "><label>'.$item['grade'].'</label>';
                        $emailtemplate = $emailtemplate.'    <span class="moreinfo">'.functions::replaceouttemplatetext($item['grade_intro']).'<br><br>'.functions::replaceouttemplatetext($item['description']).'</span>';
                        $emailtemplate = $emailtemplate.'</div>';
            
                    }
            }
        if($diagnosisID=="5"){ 
        
            $emailtemplate = $emailtemplate.'<div class="formitem radiobutton "><label>Other</label>';
            $emailtemplate = $emailtemplate.'    <span class="moreinfo"><spane>'.$diagnosisOther.'</spane></span>';
            $emailtemplate = $emailtemplate.'</div>';
        }
        $emailtemplate = $emailtemplate.'</span>';
        $emailtemplate = $emailtemplate.'</div></div>';
        $emailtemplate = $emailtemplate.'<div><div class="label"><span class="bold"><strong>Telaprevir Related:</strong></span>'; 
        $emailtemplate = $emailtemplate.'<span>';
        
        $reldetails = functions::gettelaprevirrelatedoptions($telarelated); 
        
        $emailtemplate = $emailtemplate.'<div class="formitem radiobutton "><label>'.$reldetails['name'].'</label>'; 
        $emailtemplate = $emailtemplate.'    <span class="moreinfo">'.$reldetails['text'].'</span>'; 
        $emailtemplate = $emailtemplate.'</div>'; 

        $emailtemplate = $emailtemplate.'</span>'; 
        $emailtemplate = $emailtemplate.'</div></div>'; 
    
    
        $emailtemplate = $emailtemplate.'<div><div class="label"><span class="bold"><strong>Management:</strong> <span style="font-weight:normal;"><i>Please read the following attachments</i></span></span> '; 
        $emailtemplate = $emailtemplate.'<span>'; 
        
        
        $toattach = db::runQuery('select * from |referral_attachments| order by ID asc',0,99);
        
        foreach($toattach as $item){
            
            if(functions::patienthasreferalattachementbyid($patientdata['ID'],$item['ID'])){
              
            $emailtemplate = $emailtemplate.'<div class="formitem checkbox"><label><a href="'.BASEDOMAIN.$item['filelocation'].$item['filename'].'" target="_blank">'.$item['fileDescription'].'</a></label></div>';
        
            }
        }
        
        $emailtemplate = $emailtemplate.'</span>';
        $emailtemplate = $emailtemplate.'</div>';
    
        $emailtemplate = $emailtemplate.'<div><label class="label"><span class="bold"><strong>Follow up recommendation: </strong><span style="font-weight:normal;"><i>(blank if not required)</i></span></span> <span>'.$followuprecommendation.'</span></label></div>';
    
        $emailtemplate = $emailtemplate.'<input type="hidden" name="saveonly" id="saveonly" value="false">';

        
		
		
		$responsehistory = db::runQuery('select * from |patient_history| where history_type = 5 and patientID = '.$listingID.' order by historyID desc');
		if($responsehistory){
			$emailtemplate = $emailtemplate.'<h1 class="formheader">Additional Referral Response</h1>';
		}
		
        if($responsehistory){
		foreach($responsehistory as $response){
			
             $emailtemplate = $emailtemplate.'<div><b>From '.functions::replaceouttemplatetext_doctor('Dr Gregory Siller',$response['doctorID']).' on '.$response['history_created'].'</b>,<br>'.$response['addresponse'].'</div><br><br>';
                    
		}
        }

            
            return $emailtemplate;
        
    }
	
	
}
?>
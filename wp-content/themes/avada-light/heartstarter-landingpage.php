<?php
// Template Name: Landing Page - Heart Starter


//get_header(); global $data; 

$cachevar = "?".md5(strtotime("now"));
?>

<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="de">  <!--<![endif]-->

<head>

	<!-- META DATA -->
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<meta charset="UTF-8">
	<title>Australia Wide First Aid</title>
	<meta name="title" content="Australia Wide First Aid" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
       <link rel="icon" type="image/png" href="https://www.australiawidefirstaid.com.au/images/favicon.ico">


	<!-- GOOGLE WEB FONTS INCLUDE -->
	<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400,600,700' rel='stylesheet' type='text/css'>
	
	<!-- JARVIS THEME STYLESHEETS -->
     
	<link rel="stylesheet" href="/lp/j/css/skeleton.css<?php echo $cachevar; ?>" type="text/css">
	<link rel="stylesheet" href="/lp/j/css/reset.css<?php echo $cachevar; ?>" type="text/css">    
	<link rel="stylesheet" href="/lp/j/css/style.css<?php echo $cachevar; ?>" type="text/css">
	<link rel="stylesheet" href="/lp/j/css/social.css<?php echo $cachevar; ?>" type="text/css">             
	<link rel="stylesheet" href="/lp/j/css/flexslider.css<?php echo $cachevar; ?>" type="text/css">	
	<link rel="stylesheet" href="/lp/j/css/prettyPhoto.css<?php echo $cachevar; ?>" type="text/css" media="screen"><!-- PrettyPhoto -->	
	<link rel="stylesheet" href="/lp/j/css/font-awesome.css<?php echo $cachevar; ?>" type="text/css"><!-- PrettyPhoto -->    
	<link rel="stylesheet" href="/lp/j/css/shortcodes.css<?php echo $cachevar; ?>" type="text/css"> 	
	<link rel="stylesheet" href="/lp/j/css/media.css<?php echo $cachevar; ?>"><!-- Media Queries -->
    <link rel="stylesheet" href="/lp/j/css/awfa-hs.css<?php echo $cachevar; ?>" type="text/css"> 	 
    <link rel='stylesheet' id='gforms_reset_css-css'  href='https://www.australiawidefirstaid.com.au/wp-content/plugins/gravityforms/css/formreset.min.css?ver=1.9.9' type='text/css' media='all' />
<link rel='stylesheet' id='gforms_formsmain_css-css'  href='https://www.australiawidefirstaid.com.au/wp-content/plugins/gravityforms/css/formsmain.min.css?ver=1.9.9' type='text/css' media='all' />
<link rel='stylesheet' id='gforms_ready_class_css-css'  href='https://www.australiawidefirstaid.com.au/wp-content/plugins/gravityforms/css/readyclass.min.css?ver=1.9.9' type='text/css' media='all' />
<link rel='stylesheet' id='gforms_browsers_css-css'  href='https://www.australiawidefirstaid.com.au/wp-content/plugins/gravityforms/css/browsers.min.css?ver=1.9.9' type='text/css' media='all' />
         
    
 	<link id="layout_color" href="/lp/j/css/light.css<?php echo $cachevar; ?>" rel="stylesheet" type="text/css">  
    <link id="primary_color_scheme" href="/lp/j/css/colors/yellow.css<?php echo $cachevar; ?>" rel="stylesheet" type="text/css">      
       
    <!--[if lt IE 9]>
	<script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
		   
    

	<script>
		function upm_pop_img(src, w, h, name, title)
		{
			var ww = parseInt(w);
			var wh = parseInt(h);
		
			if (ww < 100)
			{
				ww = 125;
			}
		
			if (wh < 100)
			{
				wh = 125;
		
				ww += 75;
			}
		
			var scroll = false;
		
			if (screen.width && (screen.width < ww))
			{
				scroll = 'yes';
				ww = screen.width;
			}
		
			if (screen.height && (screen.height < wh))
			{
				scroll = 'yes';
				wh = screen.height;
			}
		
			if (!title)
			{
				title = name;
			}
		
			var t = (screen.height) ? (screen.height - wh) / 2 : 0;
			var l = (screen.width) ? (screen.width - ww) / 2 : 0;
		
			var upm_pop_win = window.open('', 'upm_pop_win', 'top = '+t+', left = '+l+', width = '+ww+', height = '+wh+', toolbar = no, location = no, directories = no, status = no, menubar = no, scrollbars = '+scroll+', copyhistory = no, resizable = yes');
		
			upm_pop_win.document.writeln('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">');
			upm_pop_win.document.writeln('<html>');
			upm_pop_win.document.writeln('<head>');
			upm_pop_win.document.writeln('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">');
			upm_pop_win.document.writeln('<meta http-equiv="imagetoolbar" content="no">');
			upm_pop_win.document.writeln('<title>'+title+'</title>');
			upm_pop_win.document.writeln('<style type="text/css">');
			upm_pop_win.document.writeln('<!--');
			upm_pop_win.document.writeln('body {');
			upm_pop_win.document.writeln('margin: 0;');
			upm_pop_win.document.writeln('padding: 0;');
			upm_pop_win.document.writeln('color: #000;');
			upm_pop_win.document.writeln('background-color: #fff;');
			upm_pop_win.document.writeln('text-align: center;');
		
			if (scroll == false)
			{
				upm_pop_win.document.writeln('overflow: hidden;');
			}
		
			upm_pop_win.document.writeln('}');
		
			upm_pop_win.document.writeln('');
			upm_pop_win.document.writeln('img {');
			upm_pop_win.document.writeln('margin: 0 auto;');
			upm_pop_win.document.writeln('padding: 0;');
			upm_pop_win.document.writeln('border: none;');
		
			if (wh == 125)
			{
				upm_pop_win.document.writeln('margin-top: ' + Math.floor(50-(h/2)) + 'px;');
			}
		
			upm_pop_win.document.writeln('}');
		
			upm_pop_win.document.writeln('-->');
			upm_pop_win.document.writeln('</style>');
			upm_pop_win.document.writeln('</head>');
			upm_pop_win.document.writeln('<body>');
			upm_pop_win.document.writeln('<div id="upm-image-view">');
			upm_pop_win.document.writeln('<img src="'+src+'" width="'+w+'" height="'+h+'" alt="">');
			upm_pop_win.document.writeln('</div>');
			upm_pop_win.document.writeln('</body>');
			upm_pop_win.document.write('</html>');
		
			upm_pop_win.document.close();
		
			upm_pop_win.focus();
		
			return false;
		}
	</script>
</head>

<body class="onepage techeffect" data-spy="scroll" data-target=".navigation">
 <div id="load"></div>
 

    	
    
    <!-- START PAGE WRAP -->    
    <div class="page-wrap">
    
    
	<!-- START HOME SECTION -->    
	<div id="home" class="home1 home-parallax">
      <div class="home-text-wrapper">
      	   <div class="container clearfix">
              <div class="home-quote">
                <h1>
                <span class="slabtext"><div>If you witnessed a cardiac arrest<br/><strong>would you know what to do?</strong></div><span></span></span>
                <!--<span class="slabtext"><div><strong>Get involved in the Heart Starter Challenge</strong><br/> and we'll donate a defibrillator to your club</div></span>-->
                
                </h1>
              </div>
           </div><!-- END CONTAINER -->
       </div><!-- END CS TEXT CONTAINER -->
       <div class="downarrowcontainer arrow bounce"></div>
	</div><!-- END HOME SECTION -->
	
	
	
    <!-- START NAVIGATION -->
    <nav class="light sticky-nav navigation">
     <!-- START CONTAINER -->	
      <div class="container clearfix">			
          <div class="four columns">			
              <!-- START LOGO -->	
              <div class="logo large">
               <a href="#home"><img src="/lp/images/logo.png" title="logo" alt="Logo"></a>
              </div>
              <!-- END LOGO -->			
          </div><!-- END FOUR COLUMNS -->   
          
                   
          <div class="twelve columns">            		
              <!-- START NAVIGATION MENU ITEMS -->
              <ul class="main-menu large nav" id="nav">
                  
                  <li><a href="#home"></a></li>                
                                  
              </ul>
              <!-- END NAVIGATION MENU ITEMS -->				
          </div><!-- END TWELVE COLUMNS -->	
      </div><!-- END CONTAINER -->	
    </nav>
    <!-- END NAVIGATION -->
	
	
	<!-- START ABOUT US SECTION -->	
	<div id="about" class="page">
	
		<div class="container">	
           <div class="row">	
			<div class="sixteen columns">            
	            	                           
			</div><!-- END SIXTEEN COLUMNS -->  
           </div><!-- END ROW -->         
          </div><!-- END CONTAINER -->
          


       
           
                
      
            
        <div class="container achievements">  

            <div class="row">
               <div class="sixteen columns">
                 <h2>90% of cardiac arrest victims will survive if a<br>defibrillator is used within the first 5 minutes</h2>
               </div><!-- END SIXTEEN COLUMNS -->
               
               <div class="four columns">
                 <div class="" >
                   <span class="milestone-count highlight">33,000</span>
                   <h6 class="milestone-details">Suffer Cardiac Arrest<br>each year</h6>
                 </div>
               </div><!-- END FOUR COLUMNS -->  

               <div class="four columns">
                 <div class="" >
                   <span class="milestone-count highlight">4 mins</span>
                   <h6 class="milestone-details"> Before they have<br>brain damage</h6>
                 </div>
               </div><!-- END FOUR COLUMNS --> 

               
               
               <div class="four columns">
                 <div class="" >
                   <span class="milestone-count highlight">2 - 5 %</span>
                   <h6 class="milestone-details">Their survival Rate without a Defibrillator</h6>
                 </div>
               </div><!-- END FOUR COLUMNS --> 
               
               <div class="four columns">
                 <div class="" >
                   <span class="milestone-count highlight">11 mins</span>
                   <h6 class="milestone-details">Average for an ambulance to arrive</h6>
                 </div>
               </div><!-- END FOUR COLUMNS -->                                                                                   
           </div><!-- END ROW -->
		</div><!-- END CONTAINER --> 
        
        		
	  </div>
	  <!-- End ABOUT US SECTION -->
	

	
	
	<!-- START SERVICES SECTION -->
	<div id="services" class="page" style="padding-top:0px;">    
                     

<div class="fullwidth grey">       
          
		<div class="container clearfix">
           
					<div class="row">
        	
						<div class="one-third column">
			              <!-- START service-box -->
							<div class="service-box">
			                 <!-- START ICON -->
			                   <div>
			                      <h3>Steve's Story</h3><!-- END service-box TITLE -->
			                      <img src="/lp/j/images/steve.jpg">
			                   </div>
			                 <!-- END ICON -->  
			                 
			                <!-- START service-box DESCIPTION --> 
			                <div class="service-description" style="color:#000 !important; font-weight:400;">
			                   <p><strong>Steve Adamson suffered from cardiac arrest after joining in a football match.</strong></p><!-- END service-box DESCRIPTION --> 
			                <p>"Quite simply I wouldn't be here if the club didn't have a defibrillator. All sport clubs should have a defib, it saved my life."</p><!-- END service-box DESCRIPTION --> 
			                
			                </div>
			
			                <!-- START service-box DESCIPTION --> 
			                 
			               </div> <!-- END service-box -->       
						</div> <!-- END ONETHIRD COLUMN --> 
								
			
			
			
						<div class="one-third column">
			              <!-- START service-box -->
							<div class="service-box">
			                 <!-- START ICON -->
			                   <div>
			                      <h3>Lloyd's Story</h3><!-- END service-box TITLE -->
			                      <img src="/lp/j/images/geoff.jpg">
			                   </div>
			                 <!-- END ICON -->  
			                 
			                <!-- START service-box DESCIPTION --> 
			                <div class="service-description" style="color:#000 !important; font-weight:400;">
			                   <p><strong>Lloyd Clausen is an electrician who suffered cardiac arrest whilst working on site.</strong></p><!-- END service-box DESCRIPTION -->
			                   <p>"Doctors told me I only had 2% chance of survival if a defibrillator wasn’t administered, I am so lucky the building I was working in had an onsite defib."</p><!-- END service-box DESCRIPTION -->
			
			                </div> 
			                
			                <!-- START service-box DESCIPTION --> 
			                 
			               </div> <!-- END service-box --> 
						</div> <!-- END ONETHIRD COLUMN --> 
								
						<div class="one-third column">
			              <!-- START service-box -->
							<div class="service-box">
			                 <!-- START ICON -->
			                   <div>
			                      <h3>Joan's Story</h3><!-- END service-box TITLE -->
			                      <img src="/lp/images/joan.png">
			                   </div>
			                 <!-- END ICON -->  
			                 
			                <!-- START service-box DESCIPTION --> 
			                <div class="service-description" style="color:#000 !important; font-weight:400;">
			                  <p><strong>Joan Eacmen suffered cardiac arrest and collapsed in front her maths class.</strong></p><!-- END service-box DESCRIPTION -->
			                  <p>"I am very thankful to my students and the near by teachers who acted fast to administer CPR and locate the school’s defibrillator. There is no doubt that the defib bought me back to life and for that I am eternally grateful."</p><!-- END service-box DESCRIPTION -->
			
			                </div>
			                <!-- START service-box DESCIPTION --> 
			
			                 
			               </div> <!-- END service-box -->       
						</div> <!-- END ONETHIRD COLUMN --> 
			             			
					 </div>
			
    </div> <!-- END CONTAINER -->  
                   
</div>
	<!-- END CONTAINER -->       

	</div>
	<!-- END SERVICES SECTION -->



<!-- START ABOUT US SECTION -->	
	<div id="about" class="page">
	
		<div class="container">	
           <div class="row">	
			<div class="sixteen columns">            
	            <!-- START TITLE -->	            
				<div class="title">
				  <h1><strong>Take up the challenge</strong></h1>
                  <div class="subtitle">
                      <p>Enrol at least 30 members, supporters, employees, or friends to participate in an Australia Wide First Aid course and we will 
                      	donate a Life Pak defibrillator to your organisation. Get involved with the <strong>Heart Starter Challenge</strong> today.</p>
                      
                  </div><!-- END SUBTITLE -->
                </div><!-- END TITLE -->  	                           
			</div><!-- END SIXTEEN COLUMNS -->  
           </div><!-- END ROW -->         
          </div><!-- END CONTAINER -->
          



<div class="fullwidth grey whatwedo">
        <div class="container">  
        <div class="fancy-header2">       
           <h2 style="font-size:45px !important;">How do I get <strong>30 people</strong> to sign up?</h2>
           <div class="subtitle">
               <p>Simply register your interest and we’ll guide you through the Heart Starter Challenge. This includes sending out a resource kit to a person you nominate as your Heart Starter Ambassador.</p>
           </div>
        </div>
           
         <div class="row">          
                <div class="one-third column columnbutton">
                  <a onclick="gocontact();" href="#gocontact">
                  <div class="service-features">
                  <div>
                    <img src="/lp/images/icons/icons/register.png" alt="Service Features">
                  </div>  
                    <h3>Register</h3>                
                    <p>Register your club<br>to the Heart Starter Challenge</p>
                  </div>   
                  </a>            
                </div><!-- END ONE THIRD COLUMN -->
                
                <div class="one-third column columnbutton">
                  <a href="#courses" onclick="togglecourselocations();">
                  <div class="service-features">
                  <div>
                    <img src="/lp/j/images/icons/icons/person.png" alt="Ambassador">
                  </div>  
                    <h3>Ambassador<div style="background-color: #000000"></div></h3>                
                    <p>Get your Heart Starter Ambassador<br>to sign up 30 participants</p>
                  </div>   
                  </a>            
                </div><!-- END ONE THIRD COLUMN -->
                
                <div class="one-third column columnbutton">
                  <a target="_blank" href="http://www.physio-control.com/WCProductDetails.aspx?id=2147484792">
                  <div class="service-features">
                  <div>
                    <img src="/lp/j/images/icons/icons/defib.png" alt="About AWFA">
                  </div>  
                    <h3>Defibrillator</h3>                
                    <p>Australia Wide First Aid donates<br> a Life Pak Defibrillator (AED).</p>
                  </div>   
                  </a>            
                </div><!-- END ONE THIRD COLUMN -->                
                 
                    
     
                
            </div><!-- END ROW -->  
            <div class="row" ID="courselocations">
            	<br>
            	<h2>Be your Organisation's Ambassador and you could help save a life</h2>
            	<p style="font-size:18px; color:#fff;">What kind of Organisations are eligible?</p>
            	<div class="one-third column" style="color:#fff;">
            		<div>
            				<ul class="barrow">
										<li>Sporting Teams</li>
										<li>Pubs and Clubs</li>
										<li>Not for Profits</li>
										</ul>
            		</div>
            	</div>
            	<div class="one-third column" style="color:#fff;">
            		
            		<div>
            				<ul class="barrow">
										<li>Education and Schools</li>
										<li>Scouts</li>
										<li>Community Groups</li>
										</ul>
            		</div>
            	</div>
            	<div class="one-third column" style="color:#fff;">
            		
            		<div>
            				<ul class="barrow">
										<li>Small to Corporate Businesses</li>
										<li>Health Care Industry</li>
										<li>And Many More [<a onclick="gocontact();" href="#gocontact">Enquire</a>]</li>
										</ul>
            		</div>
            	</div>
            	<div>&nbsp;<br><br></div>
            	<p style="font-size:18px; color:#fff;">As an Ambassador, gather up 30 people from your organisation, 
            		sporting team, employees and/or family and friends to attend an accredited HLTAID003 – Provide First 
            		Aid inc CPR training session and a Life Pak Defibrillator will be donated at the completion of the 
            		training session. </p>
            </div>  
            <div class="row">
            	<br>
            </div>       
          </div>                   		
		</div><!-- END CONTAINER --> 
             
           
                
	<div class="container achievements">  

            <div class="row">
               <div class="sixteen columns">
                 <div class="title"><h1>A defibrillator can jump-start a heart</h1><br><img src="/lp/images/defibrillator_3.jpg"></div>
               </div><!-- END SIXTEEN COLUMNS -->
                                                                                                 
           </div><!-- END ROW -->
		</div>
		
		<div id="parallax3" class="parallax">
			<div class="parallax-bg bg3" style="background-position: 50% 51px;"></div>
			<div class="container clearfix">
				<div class="parallax-content">
					<p class="quote">Australia Wide First Aid is Australia’s leader in innovative first aid solutions.</p>
		
				</div><!-- END PARALLAX CONTENT -->
			</div><!-- END CONTAINER -->
		</div>

    

	<!-- START PARALLAX SECTION -->
	<div id="parallax4" class="parallax blockheader">
	<div class="parallax-bg bg4"></div>
        <div class="container clearfix">
                <div class="parallax-content">
         <!-- START CONTACT DETAILS -->
         <div class="contact-details">
           <h2><span>A defibrillator can save a life </span><br>
           
           Support the Heart Starter Challenge<br>to receive your Life Saving device
           <br></h2>		
        </div>			
	    <!-- END CONTACT DETAILS -->
                </div><!-- END PARALLAX CONTENT -->
            </div><!-- END CONTAINER -->
     </div>
    <!-- END PARALLAX SECTION -->	
	
	 
	
	<!-- START CONTACT SECTION -->
	<div id="contact" class="page">
    
		<div class="container">	
           <div class="row">	
			<div class="sixteen columns">            
	            <!-- START TITLE -->	            
				<div class="title">
				  <h1>Australia's Leader in Innovative<br>First Aid Solutions</h1>
				  <p style="font-size:28px;">Call 1300 336 613</p><br><br>
				  <img src="/lp/images/logo.png" title="logo" alt="Logo">
				  <br><br><br><br>
				  <p style="font-size:15px;">RTO 31961</p>
                </div><!-- END TITLE -->  	                           
			</div><!-- END SIXTEEN COLUMNS -->  
           </div><!-- END ROW -->         
          </div><!-- END CONTAINER -->         
		<div class="container clearfix">
			<div class="sixteen columns">
				<br><br>
				<div class="termsandconditions">
      Term and conditions

			<ul>
				<li>To receive a donated Life Pack Defibrillator (AED) the company/individual/group must prepay the campaign invoice amount and book a course maximum of 30 people completing HLTAID003 - Provide First Aid including CPR, or a mix of HLTAID001 - Provide CPR course. All other course combinations will be subject to approval on application to Australia Wide First Aid.</li>
				<li>The first aid booking must comprise of one of the following</li>
					<ul>
						<li>30 people in HLTAID003 - Provide First Aid including CPR, or a mix of HLTAID001 - Provide CPR training as one group of 30 people. If there are more than 30 people completing HLTAID003 - Provide First Aid including CPR, or a mix of HLTAID001 - Provide CPR, all additional people will be charged at the full rate for the region.</li>
						<li>30 people completing HLTAID003 - Provide First Aid including CPR, or a mix of HLTAID001 - Provide CPR training as smaller groups, as long as the course invoice minimum is achieved</li>
						<li>30 people completing HLTAID003 - Provide First Aid including CPR, or a mix of HLTAID001 - Provide CPR attending our community locations (except Newmarket QLD and Browns Plains QLD) individually or in small groups</li>
					</ul>
					
				<li>This offer is for new and current customers within Australia in areas serviceable by Australia Wide First Aid. Please check with us for areas of serviceability</li>
				<li>The Life Pak AED will be couriered to the customer on receipt of payment</li>
				<li>The training must be completed within the dates of the offer. Any vouchers not used within the offer period will be void and cannot be redeemed as cash or used for training</li>
				
				<li>This offer cannot be used in combination with any other offers</li>
				<li>Invoice total for 30 people is $2700.</li>
				<li>This offer cannot be redeemed as cash</li>
				<li>This offer is for the Life Pak Express only and cannot be transferred to any other AED.</li>
				<li>Booking dates are subject to availability</li>
				<li>For rural or remote training if travel or accommodation is required the client may be subject to further fees.</li>
			</ul> 


    </div>
				<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	   
		</div> <!-- END SIXTEEN COLUMNS -->        
	</div><!-- END CONTAINER -->
 
      
	</div>
    <!-- END CONTACT SECTION -->		
	
	
   </div><!-- END PAGE WRAP --><div id="back-to-top"><a href="#">Back to Top</a></div>
   
	
           

	<!-- JARVIS THEME SCRIPTS -->
     <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>        
    <!--<script type="text/javascript" src="/lp/j/js/jquery.tweet.js"></script>-->         
    <script type="text/javascript" src="/lp/j/js/jquery.form.js<?php echo $cachevar; ?>"></script>
	<script type="text/javascript" src="/lp/j/js/jquery.queryloader2.js<?php echo $cachevar; ?>"></script>        
    <script type="text/javascript" src="/lp/j/js/modernizr-2.6.2.min.js<?php echo $cachevar; ?>"></script>  
    <script type="text/javascript" src="/lp/j/js/jquery.fitvids.js<?php echo $cachevar; ?>"></script>  
    <script type="text/javascript" src="/lp/j/js/jquery.appear.js<?php echo $cachevar; ?>"></script>  
    <script type="text/javascript" src="/lp/j/js/jquery.slabtext.min.js<?php echo $cachevar; ?>"></script>   
    <script type="text/javascript" src="/lp/j/js/jquery.fittext.js<?php echo $cachevar; ?>"></script>   
	<script type="text/javascript" src="/lp/j/js/jquery.easing.min.js<?php echo $cachevar; ?>"></script>
	<script type="text/javascript" src="/lp/j/js/jquery.parallax-1.1.3.js<?php echo $cachevar; ?>"></script>
	<script type="text/javascript" src="/lp/j/js/jquery.prettyPhoto.js<?php echo $cachevar; ?>"></script>
	<script type="text/javascript" src="/lp/j/js/jquery.sticky.js<?php echo $cachevar; ?>"></script>
	<script type="text/javascript" src="/lp/j/js/selectnav.min.js<?php echo $cachevar; ?>"></script>    
    <script type="text/javascript" src="/lp/j/js/SmoothScroll.js<?php echo $cachevar; ?>"></script>   
    <script type="text/javascript" src="/lp/j/js/jquery.flexslider-min.js<?php echo $cachevar; ?>"></script>    
    <script type="text/javascript" src="/lp/j/js/isotope.js<?php echo $cachevar; ?>"></script>    
    <script type="text/javascript" src="/lp/j/js/bootstrap-modal.js<?php echo $cachevar; ?>"></script>   
    <script type="text/javascript" src="/lp/j/js/shortcodes.js<?php echo $cachevar; ?>"></script>     
	<script type="text/javascript" src="/lp/j/js/scripts.js<?php echo $cachevar; ?>"></script>       
	
	<script type='text/javascript' src='https://www.australiawidefirstaid.com.au/wp-content/plugins/gravityforms/js/conditional_logic.min.js?ver=1.9.9'></script>
	<script type='text/javascript' src='https://www.australiawidefirstaid.com.au/wp-content/plugins/gravityforms/js/jquery.json-1.3.js?ver=1.9.9'></script>
	<script type='text/javascript' src='https://www.australiawidefirstaid.com.au/wp-content/plugins/gravityforms/js/gravityforms.min.js?ver=1.9.9'></script>     
          
          
          
  <div id="contacter-wrapper">
  	<a name="gocontact"></a>
		<?php echo(do_shortcode('[gravityform id="8" title="false" description="true" ajax="true"]')); ?>
	</div>    
	
	<script>
	jQuery(document).ready(function($) {
		
			checkmobile();
		
			$("#contacter-wrapper, #courselocations").hide();
			
			$(".downarrowcontainer").on("click",function(){
				
					$('html, body').animate({
			        scrollTop: $(".main-menu-wrapper").offset().top
			    }, 2000);
				
			});
			
			var gfdesc = $(".gform_description").html();
			var gfclose = '<a class="closeform" href="#close" onclick="closeform();"><img src="/lp/images/circleclose.png"></a>';
			
			$(".gform_description").html(gfclose+gfdesc);
			
			

	});	
	
	function closeform(){
		$("#contacter-wrapper").hide(300);
		$(".sticky-nav").show(100);
	}
	
	function gocontact(){
		
		$(".sticky-nav").hide(100);
		$("#contacter-wrapper").scrollTop(500).show(800);
		//$("body").scrollTop(500);
		
		$('html, body').animate({
	        scrollTop: $("#contacter-wrapper").offset().top
	    }, 800);
		
	}
	
	function setlocation(locatationname){
		
			$(".locationdropdown option:contains(" + locatationname + ")").attr('selected', 'selected');
			gocontact();
		
	}
	
	function togglecourselocations(){
		
			$(".sticky-nav").hide(100);
			$("#courselocations").toggle(400);
			$('html, body').animate({
	        scrollTop: $("#courselocations").offset().top
	    }, 800);
		
	}
	
	function checkmobile(){
		
			var isMobile = {
		    Android: function() {
		        return navigator.userAgent.match(/Android/i);
		    },
		    BlackBerry: function() {
		        return navigator.userAgent.match(/BlackBerry/i);
		    },
		    iOS: function() {
		        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
		    },
		    Opera: function() {
		        return navigator.userAgent.match(/Opera Mini/i);
		    },
		    Windows: function() {
		        return navigator.userAgent.match(/IEMobile/i);
		    },
		    any: function() {
		        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
		    }
		};
		
		if(isMobile.any()) {
		   //document.location="https://www.australiawidefirstaid.com.au/heart-starter/";
		}
	}
	</script>     
	<style>
		#contacter-wrapper {
				display:block;
				position:absolute;
				left:0px;
				top:0px;
				right:0px;
				bottom:0px;
				width:100% !important;
				height:100%;
				z-index:9999999999999;
				text-align:center;
				background-color:rgba(255,255,255,0.95);
		}
		
		.gform_wrapper {
				max-width:415px;
				margin-left:auto;
				margin-right:auto;
		}
		
		.closeform {
			float:right;
			margin-right:-10px;
		}
		
		#contacter-wrapper input {
			width:100%;
		}
		
		.ginput_container select {
			color:#000 !important;
		}
		
		ul.barrow {
		  list-style: none;
		  margin: 0;
		  padding-left: 0px;
		  margin-top:15px;
		}
		ul.barrow li a {
			color:#fff;
		}
		ul.barrow li a:hover {
			color:#fff;
			text-decoration:underline;
		}
		ul.barrow li {
		  line-height: 18px;
		  margin: 0;
		  padding: 0;
		  margin-bottom: 15px;
		  padding-left: 25px;
		  text-align:left;
		}
		ul.barrow li:before {
		  display: inline-block;
		  background-image: url(/wp-content/themes/Avada/images/arrow-bullet.png);
		  background-repeat: no-repeat;
		  background-position: center center;
		  background-color: rgba(16,46,70,0.6);
		  height: 18px;
		  -moz-border-radius: 75px;
		  -webkit-border-radius: 75px;
		  border-radius: 75px;
		  width: 18px;
		  content: ' ';
		  float: left;
		  margin-right: 0;
		  margin-left: -25px;
		}
		
		.gform_button {
				  height: 65px;
				  width: 100%;
				  display: block;
				  background-color: #1ac27d;
				  border-bottom: 4px solid #16a56a;
				  text-align: center;
				  padding-top: 10px;
				  font-size: 20px !important;
				  color: #fff;
				  -webkit-border-radius: 3px;
				  -moz-border-radius: 3px;
				  border-radius: 3px;
		}
		
		.columnbutton {
				border:0px solid #ccc;
				background-color:rgba(16,46,70,0.6);
				-webkit-border-radius: 3px;
				-moz-border-radius: 3px;
				border-radius: 3px;
				
		}
		
		.contact-details {
				width:80% !important;
		}
		
		.downarrow {
			width:50%;
		}
		.downarrowcontainer {
				position:absolute;
				bottom:0px;
				left:50%;

				margin-right:auto;
				margin-left:auto;
				z-index:99999999999999;
				padding-bottom:10px;
				background-repeat:no-repeat;
				background-position:top center;
				cursor: pointer; 
				cursor: hand;
				
		}
		
		@-moz-keyframes bounce {
		  0%, 20%, 50%, 80%, 100% {
		    -moz-transform: translateY(0);
		    transform: translateY(0);
		  }
		  40% {
		    -moz-transform: translateY(-30px);
		    transform: translateY(-30px);
		  }
		  60% {
		    -moz-transform: translateY(-15px);
		    transform: translateY(-15px);
		  }
		}
		@-webkit-keyframes bounce {
		  0%, 20%, 50%, 80%, 100% {
		    -webkit-transform: translateY(0);
		    transform: translateY(0);
		  }
		  40% {
		    -webkit-transform: translateY(-30px);
		    transform: translateY(-30px);
		  }
		  60% {
		    -webkit-transform: translateY(-15px);
		    transform: translateY(-15px);
		  }
		}
		@keyframes bounce {
		  0%, 20%, 50%, 80%, 100% {
		    -moz-transform: translateY(0);
		    -ms-transform: translateY(0);
		    -webkit-transform: translateY(0);
		    transform: translateY(0);
		  }
		  40% {
		    -moz-transform: translateY(-30px);
		    -ms-transform: translateY(-30px);
		    -webkit-transform: translateY(-30px);
		    transform: translateY(-30px);
		  }
		  60% {
		    -moz-transform: translateY(-15px);
		    -ms-transform: translateY(-15px);
		    -webkit-transform: translateY(-15px);
		    transform: translateY(-15px);
		  }
		}

	
	.arrow {
	  position: fixed;
	  bottom: 0;
	  left: 50%;
	  margin-left: -20px;
	  width: 40px;
	  height: 40px;
	  background-image: url("/lp/images/landingpage_downarrow_white.png");
	  background-size: contain;
	}
	
	.bounce {
	  -moz-animation: bounce 2s infinite;
	  -webkit-animation: bounce 2s infinite;
	  animation: bounce 2s infinite;
	}
	.service-description {
			margin-bottom:0px !important;
	}
	
	.service-features h3 {
			padding-top:0px !important;
	}
	
	.service-features {
		padding-top:10px;
		padding-bottom:10px;
	}
	
	</style>
	
	<script type="text/javascript">
setTimeout(function(){var a=document.createElement("script");
var b=document.getElementsByTagName("script")[0];
a.src=document.location.protocol+"//script.crazyegg.com/pages/scripts/0023/4773.js?"+Math.floor(new Date().getTime()/3600000);
a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
</script>
  	
</body>

</html>
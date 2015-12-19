<script>
	jQuery(function($){
		setCampaign("mylestones");
		
		$("#bgoverlay").hide();
		$("#bookformwrapper").hide();
		
		$("#bgoverlay").on("click", function(){
			$("#bgoverlay").toggle();
			$("#bookformwrapper").toggle();
		});
		$("#registeronline").on("click", function(){
			$("#bgoverlay").toggle();
			$("#bookformwrapper").toggle();
		});
	});
</script>
<style>
	#homepage_form {
		margin-top:0px !important;
		
	}
	
	.enrolbutton:hover {
		text-decoration:none;
	}
	.enrol-validationerror {
		position:absolute;
		left:0px;
		width:100%;
		margin-top:-30px;
		margin-left:0px;
		box-shadow: 0px 0px 30px rgba(0,0,0,0.5);
	}
	.enrol-validationerror:after {
		display:none;
	}
	#bgoverlay {
		position:fixed;
		top:0px;
		left:0px;
		bottom:0px;
		right:0px;
		background-color:#fff;
		opacity: 0.7;
		filter: alpha(opacity=70); /* For IE8 and earlier */
	}
	#bookform.container {
		padding-bottom:0px;
	}
	.worklifelogobg {
		background-image:url(/images/work-life-connect_logo.png);
		background-position:bottom center;
		background-repeat:no-repeat;
	}
	button {
		height: 62px;
		padding: 0px 28px;
		border: 0px;
		border-radius: 5px;
		-ms-border-radius: 5px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		-o-border-radius: 5px;
		font-weight: 800;
		text-transform: uppercase;
		font-size: 18px;
		letter-spacing: 0.04em;
		transition: 0.2s all;
	}
	button.red {
		background-image:url(/images/click_w.png);
		background-position:15px 15px;
		background-repeat:no-repeat;
		padding-left:60px;
	}
	.red {
		background: #CE1443;
		color: #FFF;
		margin-top:30px;
	}
	@media only screen and (max-width: 800px){
		#homepage_form {
			display:block !important;
		}
		.enrol-mini-state select, .enrol-mini-state {
			width:316px;
		}
		.enrol-mini-location select, .enrol-mini-location {
			width:316px;
		}
		.enrol-mini-course select, .enrol-mini-course {
			width:316px;
		}
		.enrol-mini-date input, .enrol-mini-date {
			width:316px;
		}
	}
</style>


<div id="bgoverlay">
	
</div>
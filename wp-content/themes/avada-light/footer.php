<?php
$showheartstart = false;

?>
		</div>
	</div>
	<?php global $data; ?>
	<?php if(!is_page_template('blank.php')): ?>
	<?php if($data['footer_widgets']): ?>
	<footer class="footer-area">
		<div class="avada-row">
			<section class="columns columns-<?php echo $data['footer_widgets_columns']; ?>">
				<article class="col">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget 1')):
				endif;
				?>
				</article>

				<article class="col">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget 2')):
				endif;
				?>
				</article>

				<article class="col">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget 3')):
				endif;
				?>
				</article>

				<article class="col last">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget 4')):
				endif;
				?>
				</article>
			</section>
		</div>
	</footer>
	<?php endif; ?>
	<?php if($data['footer_copyright']): ?>
	<footer id="footer">
		<div class="avada-row">
		<?php if($data['icons_footer']): ?>
		<?php
		if($data['icons_footer_new']) {
			$target = '_blank';
		} else {
			$target = '_self';
		}
		if(!$data['footer_icons_color']) {
			$footer_icons_color = 'Dark';
		} else {
			$footer_icons_color = $data['footer_icons_color'];
		}

		$nofollow = '';
		if($data['nofollow_social_links']) {
			$nofollow = ' rel="nofollow"';
		}
		?>
		<ul class="social-networks social-networks-light">
			<?php if($data['facebook_link']): ?>
			<li class="facebook"><a target="<?php echo $target; ?>" href="<?php echo $data['facebook_link']; ?>"<?php echo $nofollow; ?>>Facebook</a>
				<div class="popup">
					<div class="holder">
						<p>Facebook</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['twitter_link']): ?>
			<li class="twitter"><a target="<?php echo $target; ?>" href="<?php echo $data['twitter_link']; ?>"<?php echo $nofollow; ?>>Twitter</a>
				<div class="popup">
					<div class="holder">
						<p>Twitter</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['linkedin_link']): ?>
			<li class="linkedin"><a target="<?php echo $target; ?>" href="<?php echo $data['linkedin_link']; ?>"<?php echo $nofollow; ?>>LinkedIn</a>
				<div class="popup">
					<div class="holder">
						<p>LinkedIn</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['rss_link']): ?>
			<li class="rss"><a target="<?php echo $target; ?>" href="<?php echo $data['rss_link']; ?>"<?php echo $nofollow; ?>>RSS</a>
				<div class="popup">
					<div class="holder">
						<p>RSS</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['dribbble_link']): ?>
			<li class="dribbble"><a target="<?php echo $target; ?>" href="<?php echo $data['dribbble_link']; ?>"<?php echo $nofollow; ?>>Dribbble</a>
				<div class="popup">
					<div class="holder">
						<p>Dribbble</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['youtube_link']): ?>
			<li class="youtube"><a target="<?php echo $target; ?>" href="<?php echo $data['youtube_link']; ?>"<?php echo $nofollow; ?>>Youtube</a>
				<div class="popup">
					<div class="holder">
						<p>Youtube</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['pinterest_link']): ?>
			<li class="pinterest"><a target="<?php echo $target; ?>" href="<?php echo $data['pinterest_link']; ?>" class="pinterest"<?php echo $nofollow; ?>>Pinterest</a>
				<div class="popup">
					<div class="holder">
						<p>Pinterest</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['flickr_link']): ?>
			<li class="flickr"><a target="<?php echo $target; ?>" href="<?php echo $data['flickr_link']; ?>" class="flickr"<?php echo $nofollow; ?>>Flickr</a>
				<div class="popup">
					<div class="holder">
						<p>Flickr</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['vimeo_link']): ?>
			<li class="vimeo"><a target="<?php echo $target; ?>" href="<?php echo $data['vimeo_link']; ?>" class="vimeo"<?php echo $nofollow; ?>>Vimeo</a>
				<div class="popup">
					<div class="holder">
						<p>Vimeo</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['tumblr_link']): ?>
			<li class="tumblr"><a target="<?php echo $target; ?>" href="<?php echo $data['tumblr_link']; ?>" class="tumblr"<?php echo $nofollow; ?>>Tumblr</a>
				<div class="popup">
					<div class="holder">
						<p>Tumblr</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['google_link']): ?>
			<li class="google"><a target="<?php echo $target; ?>" href="<?php echo $data['google_link']; ?>" class="google"<?php echo $nofollow; ?>>Google+</a>
				<div class="popup">
					<div class="holder">
						<p>Google</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['digg_link']): ?>
			<li class="digg"><a target="<?php echo $target; ?>" href="<?php echo $data['digg_link']; ?>" class="digg"<?php echo $nofollow; ?>>Digg</a>
				<div class="popup">
					<div class="holder">
						<p>Digg</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['blogger_link']): ?>
			<li class="blogger"><a target="<?php echo $target; ?>" href="<?php echo $data['blogger_link']; ?>" class="blogger"<?php echo $nofollow; ?>>Blogger</a>
				<div class="popup">
					<div class="holder">
						<p>Blogger</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['skype_link']): ?>
			<li class="skype"><a target="<?php echo $target; ?>" href="<?php echo $data['skype_link']; ?>" class="skype"<?php echo $nofollow; ?>>Skype</a>
				<div class="popup">
					<div class="holder">
						<p>Skype</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['myspace_link']): ?>
			<li class="myspace"><a target="<?php echo $target; ?>" href="<?php echo $data['myspace_link']; ?>" class="myspace"<?php echo $nofollow; ?>>Myspace</a>
				<div class="popup">
					<div class="holder">
						<p>Myspace</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['deviantart_link']): ?>
			<li class="deviantart"><a target="<?php echo $target; ?>" href="<?php echo $data['deviantart_link']; ?>" class="deviantart"<?php echo $nofollow; ?>>Deviantart</a>
				<div class="popup">
					<div class="holder">
						<p>Deviantart</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['yahoo_link']): ?>
			<li class="yahoo"><a target="<?php echo $target; ?>" href="<?php echo $data['yahoo_link']; ?>" class="yahoo"<?php echo $nofollow; ?>>Yahoo</a>
				<div class="popup">
					<div class="holder">
						<p>Yahoo</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['reddit_link']): ?>
			<li class="reddit"><a target="<?php echo $target; ?>" href="<?php echo $data['reddit_link']; ?>" class="reddit"<?php echo $nofollow; ?>>Reddit</a>
				<div class="popup">
					<div class="holder">
						<p>Reddit</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['forrst_link']): ?>
			<li class="forrst"><a target="<?php echo $target; ?>" href="<?php echo $data['forrst_link']; ?>" class="forrst"<?php echo $nofollow; ?>>Forrst</a>
				<div class="popup">
					<div class="holder">
						<p>Forrst</p>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php if($data['custom_icon_name'] && $data['custom_icon_link'] && $data['custom_icon_image']): ?>
			<li class="custom"><a target="<?php echo $target; ?>" href="<?php echo $data['custom_icon_link']; ?>"<?php echo $nofollow; ?>><img src="<?php echo $data['custom_icon_image']; ?>" alt="<?php echo $data['custom_icon_name']; ?>" /></a>
				<div class="popup">
					<div class="holder">
						<p><?php echo $data['custom_icon_name']; ?></p>
					</div>
				</div>
			</li>
			<?php endif; ?>
		</ul>
		<?php endif; ?>
			<ul class="copyright">
				<li><?php echo $data['footer_text'] ?></li>
			</ul>
		</div>
	</footer>
	<?php endif; ?>
	<?php endif; ?>
	<?php if($showheartstart){ ?>
	<div id="heartstarter_headercamp_wraper">
			<div id="heartstarter_headercamp"> 
						<a href="http://heartstarter.australiawidefirstaid.com.au/" target="_blank">
							<img src="https://www.australiawidefirstaid.com.au/wp-content/uploads/2015/06/heartstarter_header_item.png" alt="Join the Heart Starter Challenge">
						</a>
			</div>
	</div>
<?php } ?>
	</div><!-- wrapper -->
	<?php //include_once('style_selector.php'); ?>

	<?php if(!$data['status_gmap']): ?><script type="text/javascript" src="http<?php echo (is_ssl())? 's' : ''; ?>://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false&amp;language=<?php echo substr(get_locale(), 0, 2); ?>"></script><?php endif; ?>

	<!-- W3TC-include-js-head -->

	<?php wp_footer(); ?>
	
	<div class="pwdvbz"><a href="http://vbz.com.au/" target="_blank" >Powered by Verbazend</a></div>


	<?php echo $data['space_body']; ?>
	<?php debug_time(9999999); ?>
	<style>
		.pwdvbz {
			background-color:#282a2b !important;
			text-align:center;
			display:block;
			
		}
		.pwdvbz a {
			color:#3d4041 !important;
			
		  }
		  .riskrating {
		  	border:1px solid #b0163c;
		  	padding:3px;
		  	background-color:#ffc0d0;
		  	margin-top:5px;
		  }
		  .riskrating tr td:nth-child(2), .shrtdesc tr td:nth-child(2), .shrtdesc tr td:nth-child(3), .shrtdesc tr td:nth-child(4), .shrtdesc tr td:nth-child(5) {
		  	display:none;
		  	width:0px;
		  }
		  .riskrating tr td:nth-child(1) {
		  	width:315px !important;
		  }
		  .shrtdesc tr td:nth-child(1){
		  	width:318px !important;
		  }
		  .shrtdesc tr td:nth-child(1) span{
		  	width:290px !important;
		  	display:block;
		  }
		  .shrtdesc {
		  	margin-top:9px;
		  }
		  .fancybox-outer #productdescription tr:nth-child(even), .shrtdesc tr:nth-child(odd) {
		  	background-color:#f6f6f6;
		  }
		  .shrtdesc tr td {
		  	padding-left:22px;
		  	background-image:url(/wp-content/uploads/2014/09/bullet.png);
		  	background-position:5px 8px;
		  	background-repeat:no-repeat;
		  }
		  .shrtdesc td {
		  	vertical-align:top!important;
		  }
		  .shrtdesc table {
		  	height:auto !important;
		  }
		  .ui-autocomplete {
		  	max-width:300px;
		  }
		  .headerusi {
		  	width:100%;
		  	text-align:center;
		  	display:block;
		  }

		  #headerusiwrapper {
		  	position:absolute;
		  	left:0px;
		  	right:0px;
		  	z-index:999999999;
		  	margin-top:-43px;
		  	display:none;
		  }

		  
			.courseSelectionWrapper {
				position:absolute;
				width:100%;			
				background-color:#fff;
				
				z-index:99999999999;
				top:3%;
				bottom:3%;
				right:-50%;
				left:-50%;
				padding:8px;
				display:block;
				max-width:650px;
				float:center;
				margin: 0 auto;
				margin-top:30px;
				-webkit-border-radius: 5px;
				-moz-border-radius: 5px;
				border-radius: 5px;
				max-height:100%;
				overflow:auto;
				
			}
			
			.courseSelectionWrapper h2 {
				margin:0px;
				margin-bottom:3px;
			}
			
			.courseSelectionWrapperDiv {
				position:fixed;
				width:100%;
				left:0px;
				
				bottom:0px;
				height:100%;
				background-color:rgba(0, 0, 0, 0.5);
				z-index:99999999999;
				top:0px;
				display:none;
				max-height:100%;
			}
			.center {
				width:100%;
			}
			
			.courseselectionlist .courseItem {
				background-color:#b0163c;
				color:#fff;
				border:1px solid #b0163c;
				-webkit-border-radius: 5px;
				-moz-border-radius: 5px;
				border-radius: 5px;
				padding:5px;
				padding-top:0px;
				display:inline-block;
				margin-right:5px;
				margin-bottom:5px;
				width:190px;
				height:46px;
				vertical-align:top;
			}
			.courseselectionlist a {
				color:#fff !important;
			}
			.weeksperator {
				margin-top:10px;
				font-weight:bold;
			}
			
			.courseselectionlist .courseItem .courseItemDate {
				font-weight:bold;
				
			}
			.courselocationdetails {
				font-weight:bold;
				font-size:14px;
			}
			.closebutton {
				float:right;
			}
			
			body.page-id-4641 #homepage_form {
				margin-top:-472px !important;
				z-index:999999999;
			}
			
			#courseSelectorTemplate {
				display:none;
			}
			
			#heartstarter_headercamp_wraper {
					position:absolute;
					display:block;
					top:40px;
					left:0px;
					right:0px;
					width:auto;
					max-width:940px;
					margin-left:auto;
					margin-right:auto;
			}
			
			#heartstarter_headercamp {
					position:absolute;
					display:block;
					top:0px;
					right:0px;
					width:260px;
					float:right;
					background-color:#b0163c;
					height:100px;
					z-index:999999998;
					vertical-align:top;
					-webkit-border-bottom-right-radius: 10px;
					-webkit-border-bottom-left-radius: 10px;
					-moz-border-radius-bottomright: 10px;
					-moz-border-radius-bottomleft: 10px;
					border-bottom-right-radius: 10px;
					border-bottom-left-radius: 10px;
			}
			
			#heartstarter_headercamp img {
					display:block;
					margin-left:9px;
					margin-top:6px;
			}
			
			@media only screen and (max-width: 960px) {
				#heartstarter_headercamp_wraper {
						display:none;	
				}
				
			}
			
			@media only screen and (max-width: 700px) {
				.courseSelectionWrapper h2, .courseselectionlist {
					padding-left:20px;
					padding-right:20px;
				}
				
			}
			
			@media only screen and (max-width: 700px) {
				.courseselectionlist .courseItem {
					background-color:#b0163c;
					color:#fff;
					border:1px solid #b0163c;
					-webkit-border-radius: 5px;
					-moz-border-radius: 5px;
					border-radius: 5px;
					padding:5px;
					padding-top:0px;
					display:inline-block;
					margin-right:5px;
					margin-bottom:5px;
					width:47%;
					height:46px;
					vertical-align:top;
				}
			}
			@media only screen and (max-width: 645px) {
				.courseselectionlist .courseItem {
					background-color:#b0163c;
					color:#fff;
					border:1px solid #b0163c;
					-webkit-border-radius: 5px;
					-moz-border-radius: 5px;
					border-radius: 5px;
					padding:5px;
					padding-top:0px;
					display:inline-block;
					margin-right:5px;
					margin-bottom:5px;
					width:45%;
					height:46px;
					vertical-align:top;
				}
			}
			@media only screen and (max-width: 450px) {
				.courseselectionlist .courseItem {
					background-color:#b0163c;
					color:#fff;
					border:1px solid #b0163c;
					-webkit-border-radius: 5px;
					-moz-border-radius: 5px;
					border-radius: 5px;
					padding:5px;
					padding-top:0px;
					display:inline-block;
					margin-right:5px;
					margin-bottom:5px;
					width:100%;
					height:46px;
					vertical-align:top;
				}
			}
			
			.zopim iframe[src="about:blank"]{display:block !important;}

	</style>
	<script>
		jQuery(document).ready(function($) {
			$(".various").fancybox({
				maxWidth	: 880,
				maxHeight	: 620,
				fitToView	: false,
				width		: '70%',
				height		: '70%',
				autoSize	: true,
				closeClick	: false,
				openEffect	: 'none',
				closeEffect	: 'none'
			});
			$("#headerusiwrapper").html("");
			//$(".usiinputitem").hide();
			$(".headerusi").on("click", function(){
				<?php if(is_page('Home')){ ?>
					hpscrollToElement('#content-boxes-3');
				<?php } else { ?>
					document.location='/?gottoid=content-boxes-3';
				<?php } ?>
			});
			<?php if(isset($_GET['gottoid'])) { ?>
				window.setTimeout("hpscrollToElement('#<?php echo $_GET['gottoid']; ?>');",3500);
				
			<?php } ?>
		});
		function hpscrollToElement(elementselector){
			jQuery('html, body').animate({
		        scrollTop: jQuery(elementselector).offset().top-80
		    }, 800);
		}
		
		 
	</script>
	
	<div id="courseSelectorTemplate">
	<?php
		include("form-templates/course_selector.php");
	?>
	</div>
	
	<?php
	if(isset($_GET['campaign'])){
		?>
		<script>
		jQuery(function($){
			setCampaign("<?php echo $_GET['campaign']; ?>");
		});
		</script>
		<?php
	}
	
	?>
		<!--Start of Zopim Live Chat Script-->
	<script type="text/javascript">
	window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
	d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
	_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
	$.src="//v2.zopim.com/?1eh9LYNQgfxke7fUw5dqSg8xHNFgQlTV";z.t=+new Date;$.
	type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
	</script>
	<!--End of Zopim Live Chat Script-->
	
	<script type="text/javascript">
	/* <![CDATA[ */
	var google_conversion_id = 995774513;
	var google_custom_params = window.google_tag_params;
	var google_remarketing_only = true;
	/* ]]> */
	</script>
	<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
	</script>
	<noscript>
	<div style="display:inline;">
	<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/995774513/?value=0&amp;guid=ON&amp;script=0"/>
	</div>
	</noscript>
</body>
</html>
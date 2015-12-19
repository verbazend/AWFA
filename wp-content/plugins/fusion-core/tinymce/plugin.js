(function ()
{
	// create zillaShortcodes plugin
	tinymce.create("tinymce.plugins.fusionShortcodes",
	{
		init: function ( ed, url )
		{
			ed.addCommand("fusionPopup", function ( a, params )
			{
				var popup = params.identifier;
				
				// load thickbox
				tb_show("Fusion Shortcodes", ajaxurl + "?action=fusion_shortcodes_popup&popup=" + popup + "&width=" + 800);
			});
		},
		createControl: function ( btn, e )
		{
			if ( btn == "fusion_button" )
			{	
				var a = this;
				
				if(FusionShortcodes.dev == 'false') {
					var btn = e.createButton('fusion_button', {
	                    title: "Insert Fusion Shortcode",
						image: FusionShortcodes.plugin_folder + "/tinymce/images/icon.png",
						icons: false,
						onclick: function() {
	                        tinyMCE.activeEditor.execCommand("fusionPopup", false, {
	                            title: 'Shortcode Generator',
	                            identifier: 'shortcode-generator'
	                        });

	                        jQuery('#TB_window').hide();

							return false;
						}
	                });
				}
                
                if(FusionShortcodes.dev == 'true') {
					var btn = e.createSplitButton('fusion_button', {
	                    title: "Insert Fusion Shortcode",
						image: FusionShortcodes.plugin_folder + "/tinymce/images/icon.png",
						icons: false
	                });

	                btn.onRenderMenu.add(function (c, b)
					{
						window.fusionEd = b;

                        a.addImmediate({
                            title : 'Alert',
                            icon: 'fusion_alert',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[alert type="e.g. general, error, success, notice" animation_type="none, bounce, fade, flash, snake, slide" animation_direction="up, down, left, right" animation_speed="0.1 to 1"]Your Message Goes Here.[/alert]');
                            }
                        });

                        a.addImmediate({
                            title : 'Blog',
                            icon: 'fusion_blog',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[blog number_posts="10" cat_slug="" title="yes" thumbnail="yes" excerpt="yes" excerpt_words="35" meta_all="yes" meta_author="yes" meta_categories="yes" meta_comments="yes" meta_date="yes" meta_link="yes" paging="yes" scrolling="pagination" blog_grid_columns="3" strip_html="yes" layout="large"][/blog]');
                            }
                        });

                        a.addImmediate({
                            title : 'Buttons',
                            icon: 'tfusion_button',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[button color="e.g. green, darkgreen, orange, blue, red, pink, darkgray, lightgray or leave blank" size="large or small" link="" target="" animation_type="none, bounce, fade, flash, snake, slide" animation_direction="up, down, left, right" animation_speed="0.1 to 1"]Text here[/button]');
                            }
                        });

                        a.addImmediate({
                            title : 'Checklist',
                            icon: 'fusion_checklist',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[checklist icon="check, star, arrow, asterik, cross, plus" iconcolor="#ffffff" circle="yes or no"]<ul>\r<li>Item #1</li>\r<li>Item #2</li>\r<li>Item #3</li>\r</ul>[/checklist]');
                            }
                        });

                        a.addImmediate({
                            title : 'Client Slider',
                            icon: 'fusion_client_slider',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[clients picture_size="fixed or auto"][client link="" linktarget="" image="" width="" height="" alt=""][client link="" linktarget="" image="" width="" height="" alt=""][client link="" linktarget="" image="" width="" height="" alt=""][client link="" linktarget="" image="" width="" height="" alt=""][client link="" linktarget="" image="" width="" height="" alt=""][/clients]');
                            }
                        });

                        a.addImmediate({
                            title : 'Content Boxes',
                            icon: 'fusion_content_boxes',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[content_boxes layout="icon-with-title, icon-on-top, icon-on-side, icon-boxed" iconcolor="" circlecolor="" circlebordercolor="" backgroundcolor=""]<br />[content_box title="Responsive Design" icon="tablet" image="" image_width="35" image_height="35" link="http://themeforest.net/user/ThemeFusion" linktarget="" linktext="Learn More" animation_type="none, bounce, fade, flash, snake, slide" animation_direction="up, down, left, right" animation_speed="0.1 to 1"]Avada is fully responsive and can adapt to any screen size. Try resizing your browser window to see the adaptation.[/content_box]<br />[content_box title="Awesome Sliders" icon="thumbs-up" image="" image_width="35" image_height="35" link="http://themeforest.net/user/ThemeFusion" linktarget="" linktext="Learn More" animation_type="none, bounce, fade, flash, snake, slide" animation_direction="up, down, left, right" animation_speed="0.1 to 1"]Avada includes the awesome Layer Parallax Slider as well as the popular FlexSlider2. Both are super easy to use![/content_box]<br />[content_box title="Unlimited Colors"  icon="magic" image="" image_width="35" image_height="35" link="http://themeforest.net/user/ThemeFusion" linktarget="" linktext="Learn More" animation_type="none, bounce, fade, flash, snake, slide" animation_direction="up, down, left, right" animation_speed="0.1 to 1"]We included a backend color picker for unlimited color options. Anything can be changed, including the gradients![/content_box]<br />[content_box title="500+ Google Fonts" icon="beaker" image="" image_width="35" image_height="35" link="http://themeforest.net/user/ThemeFusion" linktarget="" linktext="Learn More" animation_type="none, bounce, fade, flash, snake, slide" animation_direction="up, down, left, right" animation_speed="0.1 to 1"]Avada loves fonts, choose from over 500+ Google Fonts. You can change all headings and body copy with ease![/content_box]<br />[/content_boxes]');
                            }
                        });

                        a.addImmediate({
                            title : 'Counters Circle',
                            icon: 'fusion_counters_circle',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[counters_circle][counter_circle filledcolor="" unfilledcolor="" size="" speed="" value="75"]75%[/counter_circle][counter_circle filledcolor="" unfilledcolor="" value="30"][fontawesome icon="adjust" circle="no" size="large"][/counter_circle][counter_circle filledcolor="" unfilledcolor="" value="70"]7/10[/counter_circle][counter_circle filledcolor="" unfilledcolor="" value="50"]Title[/counter_circle][/counters_circle]');
                            }
                        });

                        a.addImmediate({
                            title : 'Counters Box',
                            icon: 'fusion_counters_box',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[counters_box][counter_box value="2000" unit="%"]Counter Title Goes Here[/counter_box][counter_box value="155" unit="%"]Counter Title Goes Here[/counter_box][counter_box value="65" unit="%"]Counter Title Goes Here[/counter_box][counter_box value="85" unit="%"]Counter Title Goes Here[/counter_box][/counters_box]');
                            }
                        });

                        a.addImmediate({
                            title : 'Dropcap',
                            icon: 'fusion_dropcap',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[dropcap]...[/dropcap]');
                            }
                        });

                        a.addImmediate({
                            title : 'Flexslider',
                            icon: 'fusion_flexslider',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[flexslider layout="posts, posts-with-excerpt, attachments" excerpt="25" category="" limit="3" id="" lightbox="yes or no (only works with attachments layout)"][/flexslider]');
                            }
                        });

                        a.addImmediate({
                            title : 'FontAwesome',
                            icon: 'fusion_fontawesome',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[fontawesome icon="adjust" circle="yes or no" size="large medium or small" iconcolor="" circlecolor="" circlebordercolor="" animation_type="none, bounce, fade, flash, snake, slide" animation_direction="up, down, left, right" animation_speed="0.1 to 1"]');
                            }
                        });

                        a.addImmediate({
                            title : 'Full Width',
                            icon: 'fusion_full',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[fullwidth backgroundcolor="" backgroundimage="" backgroundrepeat="no-repeat" backgroundposition="top left" backgroundattachment="scroll" bordersize="1px" bordercolor="" paddingTop="20px" paddingBottom="20px"]...[/fullwidth]');
                            }
                        });

                        a.addImmediate({
                            title : 'Google Map',
                            icon: 'fusion_map',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[map address="" type="roadmap, satellite, hybrid, terrain" width="100%" height="300px" zoom="14" scrollwheel="yes" scale="yes" zoom_pancontrol="yes"][/map]');
                            }
                        });

                        a.addImmediate({
                            title : 'Highlight',
                            icon: 'fusion_highlight',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[highlight color="eg. yellow, black or #333333"]...[/highlight]');
                            }
                        });

                        a.addImmediate({
                            title : 'Image Frames',
                            icon: 'fusion_img_frames',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[imageframe style="none,border,glow,dropshadow,bottomshadow" bordercolor="" bordersize="4px" stylecolor="" align="" animation_type="none, bounce, fade, flash, snake, slide" animation_direction="up, down, left, right" animation_speed="0.1 to 1"]<img src="Image Link" alt="Image Description" />[/imageframe]');
                            }
                        });

                        a.addImmediate({
                            title : 'Images Carousel',
                            icon: 'fusion_img_carousel',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[images picture_size="fixed or auto" lightbox="yes or no"][image link="" linktarget="" image="" width="" height="" alt=""][image link="" linktarget="" image="" width="" height="" alt=""][image link="" linktarget="" image="" width="" height="" alt=""][image link="" linktarget="" image=""][image link="" linktarget="" image="" width="" height="" alt=""][/images]');
                            }
                        });

                        a.addImmediate({
                            title : 'Lightbox',
                            icon: 'fusion_lightbox',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '<a title="lightbox description" href="full image link" rel="prettyPhoto"><img alt="lightbox title" src="thumbnail image link" /></a>');
                            }
                        });

                        a.addImmediate({
                            title : 'Person',
                            icon: 'fusion_person',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[person name="John Doe" pic_link="" picture="" title="Developer" email="" facebook="http://facebook.com" twitter="http://twitter.com" linkedin="http://linkedin.com" dribbble="http://dribbble.com" linktarget=""]Redantium, totam rem aperiam, eaque ipsa qu ab illo inventore veritatis et quasi architectos beatae vitae dicta sunt explicabo. Nemo enim.[/person]');
                            }
                        });

                        a.addImmediate({
                            title : 'Progress Bar',
                            icon: 'fusion_progress_bar',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[progress percentage="60" unit="%" filledcolor="" unfilledcolor=""]Web Design[/progress]');
                            }
                        });

                        a.addImmediate({
                            title : 'Pricing Table',
                            icon: 'fusion_pricing_table',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[pricing_table type="e.g. 1 or 2" backgroundcolor="" bordercolor="" dividercolor=""][pricing_column title="Standard"][pricing_price currency="$" price="15.55" time="monthly"][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column][/pricing_table]');
                            }
                        });

                        a.addImmediate({
                            title : 'Recent Posts',
                            icon: 'fusion_recent_posts',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[recent_posts layout="default, thumbnails-on-side, date-on-side" columns="1-4" number_posts="4" cat_slug="" exclude_cats="" thumbnail="yes" title="yes" meta="yes" excerpt="yes" excerpt_words="15" strip_html="yes" animation_type="none, bounce, fade, flash, snake, slide" animation_direction="up, down, left, right" animation_speed="0.1 to 1"][/recent_posts]');
                            }
                        });

                        a.addImmediate({
                            title : 'Recent Works',
                            icon: 'fusion_recent_works',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[recent_works layout="carousel, grid, grid-with-excerpts" filters="yes or no" columns="1-4" cat_slug="" number_posts="10" excerpt_words="15" animation_type="none, bounce, fade, flash, snake, slide" animation_direction="up, down, left, right" animation_speed="0.1 to 1"][/recent_works]');
                            }
                        });

                        a.addImmediate({
                            title : 'Separator',
                            icon: 'fusion_separator',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[separator top="40" style="none, single, double, dashed, dotted, shadow"]');
                            }
                        });

                        a.addImmediate({
                            title : 'Sharing Box',
                            icon: 'fusion_sharing_box',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[sharing tagline="Share This Story, Choose Your Platform!" title="Title To Share" link="http://google.com" description="A small description of the page" backgroundcolor=""][/sharing]');
                            }
                        });

                        a.addImmediate({
                            title : 'Slider',
                            icon: 'fusion_slider',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[slider width="100%" height="100%"][slide type="video"][vimeo id="10145153" width="600" height="350"][/slide][slide link="" linktarget="" lightbox="yes or no"]image link here[/slide][/slider]');
                            }
                        });

                        a.addImmediate({
                            title : 'SoundCloud',
                            icon: 'fusion_soundcloud',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[soundcloud url="http://api.soundcloud.com/tracks/15565763" comments="yes" auto_play="no" color="ff7700" width="100%" height="81"]');
                            }
                        });

                        a.addImmediate({
                            title : 'Social Links',
                            icon: 'fusion_social_links',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[social_links colorscheme="" linktarget="" rss="" facebook="" twitter="" dribbble="" google="" linkedin="" blogger="" tumblr="" reddit="" yahoo="" deviantart="" vimeo="" youtube="" pinterest="" digg="" flickr="" forrst="" myspace="" skype="" show_custom="no"]');
                            }
                        });

                        a.addImmediate({
                            title : 'Tabs',
                            icon: 'fusion_tabs',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[tabs tab1=\"Tab 1\" tab2=\"Tab 2\" tab3=\"Tab 3\" layout="horizontal or vertical" backgroundcolor="" inactivecolor=""]<br /><br />[tab id=1]Tab content 1[/tab]<br />[tab id=2]Tab content 2[/tab]<br />[tab id=3]Tab content 3[/tab]<br /><br />[/tabs]');
                            }
                        });

                        a.addImmediate({
                            title : 'Tagline Box',
                            icon: 'fusion_tagline',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[tagline_box backgroundcolor="" shadow="no" shadowopacity="0.1-1" border="1px" bordercolor="" highlightposition="right, left, top or bottom" link="http://themeforest.net/user/ThemeFusion" linktarget="" buttoncolor="e.g. green, darkgreen, orange, blue, red, pink, darkgray, lightgray or leave blank" button="Purchase Now" title="Avada is incredibly responsive, with a refreshingly clean design" description="And it has some awesome features, premium sliders, unlimited colors, advanced theme options and so much more!"][/tagline_box]');
                            }
                        });

                        a.addImmediate({
                            title : 'Testimonial',
                            icon: 'fusion_testimonial',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[testimonials backgroundcolor="" textcolor=""]<br />[testimonial name="John Doe" gender="male or female" company="My Company" link="" target=""]"Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consec tetur, adipisci velit, sed quia non numquam eius modi tempora incidunt utis labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minimas veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur slores amet untras vel illum qui."[/testimonial]<br />[testimonial name="Doe John" gender="male or female" company="My Company" link="" target=""]"Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consec tetur, adipisci velit, sed quia non numquam eius modi tempora incidunt utis labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minimas veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur slores amet untras vel illum qui."[/testimonial]<br />[/testimonials]');
                            }
                        });

                        a.addImmediate({
                            title : 'Title',
                            icon: 'fusion_title',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[title size="1 to 6"]Title[/title]');
                            }
                        });

                        a.addImmediate({
                            title : 'Toggles',
                            icon: 'fusion_toggle',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[accordian][toggle title="Title" open="yes"]...[/toggle][toggle title="Title" open="no"]...[/toggle][toggle title="Title" open="no"]...[/toggle][toggle title="Title" open="no"]...[/toggle][toggle title="Title" open="no"]...[/toggle][/accordian]');
                            }
                        });

                        a.addImmediate({
                            title : 'Tooltip',
                            icon: 'fusion_tooltip',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[tooltip title="Text Tooltip"]Hover over this text for Tooltip[/tooltip]');
                            }
                        });

                        a.addImmediate({
                            title : 'Table',
                            icon: 'fusion_table',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '<div class="table-1"> \
<table width="100%"> \
<thead> \
<tr> \
<th>Column 1</th> \
<th>Column 2</th> \
<th>Column 3</th> \
<th>Column 4</th> \
</tr> \
</thead> \
<tbody> \
<tr> \
<td>Item #1</td> \
<td>Description</td> \
<td>Subtotal:</td> \
<td>$1.00</td> \
</tr> \
<tr> \
<td>Item #2</td> \
<td>Description</td> \
<td>Discount:</td> \
<td>$2.00</td> \
</tr> \
<tr> \
<td>Item #3</td> \
<td>Description</td> \
<td>Shipping:</td> \
<td>$3.00</td> \
</tr> \
<tr> \
<td>Item #4</td> \
<td>Description</td> \
<td>Tax:</td> \
<td>$4.00</td> \
</tr> \
<tr> \
<td><strong>All Items</strong></td> \
<td><strong>Description</strong></td> \
<td><strong>Your Total:</strong></td> \
<td><strong>$10.00</strong></td> \
</tr> \
</tbody> \
</table>');
                            }
                        });

                        a.addImmediate({
                            title : 'Vimeo',
                            icon: 'fusion_vimeo',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[vimeo id="Enter video ID (eg. 10145153)" width="600" height="350" autoplay="no"]');
                            }
                        });

                        a.addImmediate({
                            title : 'Woo Featured',
                            icon: 'fusion_woo',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[featured_products_slider]');
                            }
                        });

                        a.addImmediate({
                            title : 'Woo Products',
                            icon: 'fusion_woo',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[products_slider picture_size="fixed or auto" cat_slug="" number_posts="5" show_cats="no" show_price="no" show_buttons="no"]');
                            }
                        });

                        a.addImmediate({
                            title : 'Youtube',
                            icon: 'fusion_youtube',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[youtube id="Enter video ID (eg. Wq4Y7ztznKc)" width="600" height="350" autoplay="no"]');
                            }
                        });

                        a.addImmediate({
                            title : '1/2',
                            icon: 'fusion_half',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[one_half last="no"]...[/one_half]');
                            }
                        });

                        a.addImmediate({
                            title : '1/3',
                            icon: 'fusion_third',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[one_third last="no"]...[/one_third]');
                            }
                        });

                        a.addImmediate({
                            title : '2/3',
                            icon: 'fusion_two_third',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[two_third last="no"]...[/two_third]');
                            }
                        });

                        a.addImmediate({
                            title : '1/4',
                            icon: 'fusion_fourth',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[one_fourth last="no"]...[/one_fourth]');
                            }
                        });

                        a.addImmediate({
                            title : '3/4',
                            icon: 'fusion_three_fourth',
                            onclick : function() {
                                tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[three_fourth last="no"]...[/three_fourth]');
                            }
                        });
					});
                }

                return btn;
			}
			
			return null;
		},
		addImmediate: function (obj) {
			window.fusionEd.add({
				title: obj.title,
				icon: obj.icon,
				onclick: obj.onclick
			})
		},
		getInfo: function () {
			return {
				longname: 'Fusion Shortcodes',
				author: 'ThemeFusion',
				authorurl: 'http://theme-fusion.com',
				infourl: 'http://wiki.moxiecode.com/',
				version: "1.0"
			}
		}
	});

	tinymce.PluginManager.add("fusionShortcodes", tinymce.plugins.fusionShortcodes);
})();
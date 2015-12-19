	$(document).ready(function(){
		$("#header .menu").superfish();
		
		$("#slider").nivoSlider({
		effect:'fade', // Specify sets like: 'fold,fade,sliceDown'
        slices:15, // For slice animations
        boxCols: 8, // For box animations
        boxRows: 4, // For box animations
        animSpeed:500, // Slide transition speed
        pauseTime:5000, // How long each slide will show
        startSlide:0, // Set starting Slide (0 index)
        directionNav:false, // Next & Prev navigation
        directionNavHide:false, // Only show on hover
        controlNav:true, // 1,2,3... navigation
        controlNavThumbs:false, // Use thumbnails for Control Nav
        controlNavThumbsFromRel:false, // Use image rel for thumbs
        controlNavThumbsSearch: '.jpg', // Replace this with...
        controlNavThumbsReplace: '_thumb.jpg', // ...this in thumb Image src
        keyboardNav:true, // Use left & right arrows
        pauseOnHover:true, // Stop animation while hovering
        manualAdvance:false, // Force manual transitions
        captionOpacity:0, // Universal caption opacity
        prevText: 'Prev', // Prev directionNav text
        nextText: 'Next', // Next directionNav text
        beforeChange: function(){}, // Triggers before a slide transition
        afterChange: function(){}, // Triggers after a slide transition
        slideshowEnd: function(){}, // Triggers after all slides have been shown
        lastSlide: function(){}, // Triggers when last slide is shown
        afterLoad: function(){} // Triggers when slider has loaded
		});
		
		$("#footer .menu li").last().addClass("noborder");
		
		
		
		$('.scrollPage').click(function() {
		   var elementClicked = $(this).attr("href");
		   var destination = $(elementClicked).offset().top;
		   $("html:not(:animated),body:not(:animated)").animate({ scrollTop: destination-20}, 500 );
		   return false;
		});
		//$("select").uniform();
		$(".update2").uniform();
		
	});
	
	
					
    });
$(document).ready(function() {

	var defaultName = 'My Company';

	// Set input and company name to default on load
	$('input#name').val(defaultName);
	$('.name').html(defaultName);
	
	// Remove text from input on click when equal to default
	$('input#name').on("click", function() {
    if ($(this).val() == defaultName)
        $(this).val("")
	});
	
	// On input change, change company name
	$('input#name').keyup(function() {

		$('.name').empty().html( $(this).val() );

		// if input is empty set name to default
		if( $(this).val() == 0) {
		
			$('.name').html(defaultName);
		
		}
		
	});	
	
	// On focus change, change company name
	$('input#name').focusout(function() {

		$('.name').empty().html( $(this).val() );

		// if input is empty set name to default
		if( $(this).val() == 0) {
		
			$('.name').html(defaultName);
		
		}
		
	});
	
	
	// Preload dark images -- no lag on img change
	function preloadImg(src) {
    $('<img/>')[0].src = src;
	}

	preloadImg('images/badge-1_dark.png');
	preloadImg('images/badge-2_dark.png');
	preloadImg('images/badge-3_dark.png');
	preloadImg('images/badge-4_dark.png');
	
	
	// Change badge background and img on radio change
	$('.scheme').change(function() {
	
		if( $('.scheme#dark').is(':checked') ) {
		
			// Add dark class and change img
			$('.badge').addClass('dark');
			$('.badge-img').each(function(){
				$(this).attr('src', $(this).attr('src').replace('.png','_dark.png') );
				
			});
			
		} else {
		
			// Remove dark class and change img back
			$('.badge').removeClass('dark');
			$('.badge-img').each(function(){
				$(this).attr('src', $(this).attr('src').replace('_dark.png','.png') );
				
			});
			
		}
		
	});
	
	// Change background transparency on checkbox change
	$('.background').change(function() {
	
		if( $('.background#transparent').is(':checked') ) {
			
			// Add transparent class
			$('.badge').addClass('transparent');
			
		} else {
		
			// Remove transparent class
			$('.badge').removeClass('transparent');
		
		}
	
	});






	// Change badge class and output text on badge selection change
	$('input#name, .scheme, .background, input[type="radio"].badge-select').change(function() {
	
		$('.badge').removeClass('selected');
		$('input[type="radio"].badge-select:checked').siblings('label').children('.badge').addClass('selected');

		// Reset and Create output
		var outputBase = location.protocol + '//' + location.host;
		var outputStyle = '&lt;link rel="stylesheet" type="text/css" href="' + outputBase + '/embed-codes/style.css"&gt;';
		var badgeOutput = $('.badge.selected').parent().html();
				badgeOutput = badgeOutput.replace('/embed-codes/images/', outputBase + '/embed-codes/images/').replace(' selected','');
				badgeOutput = outputStyle + badgeOutput;
				badgeOutput = badgeOutput.replace(/(\r\n|\n|\r)/gm,"").replace(/\t/g, '').replace('<img','<a href="http://www.australiawidefirstaid.com.au/"><img').replace('class="badge-img">','class="badge-img"></a>').replace('<span class="name">','<a href="http://www.australiawidefirstaid.com.au/"><span class="name">').replace('<span class="label">','<a href="http://www.australiawidefirstaid.com.au/"><span class="label">').replace('</span>','</span></a>');
		
		$('textarea.output').html( badgeOutput );

	});
	
	
	
	
	
});
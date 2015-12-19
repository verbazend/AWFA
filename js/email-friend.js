jQuery.getScript(location.protocol + '//s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e65c542703d1faa');
jQuery(function($) {

  //Email links
  $('.email').live('click', function() {
    if (window.addthis_sendto) {
      addthis_sendto('email');
      return false;
    } else {
      var href = 'mailto:?';
      href += 'subject=' + encodeURIComponent(document.title) + '&';
      href += 'body=' + encodeURIComponent(location.href);
      $(this).attr('href', href);
    }
  });

});
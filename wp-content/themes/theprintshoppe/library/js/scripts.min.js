/*
 * Bones Scripts File
 * Author: Eddie Machado
 *
 * This file should contain any js scripts you want to add to the site.
 * Instead of calling it in the header or throwing it inside wp_head()
 * this file will be called automatically in the footer so as not to
 * slow the page load.
 *
 * There are a lot of example functions and tools in here. If you don't
 * need any of it, just remove it. They are meant to be helpers and are
 * not required. It's your world baby, you can do whatever you want.
*/


/*
 * Get Viewport Dimensions
 * returns object with viewport dimensions to match css in width and height properties
 * ( source: http://andylangton.co.uk/blog/development/get-viewport-size-width-and-height-javascript )
*/
function updateViewportDimensions() {
	var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],x=w.innerWidth||e.clientWidth||g.clientWidth,y=w.innerHeight||e.clientHeight||g.clientHeight;
	return { width:x,height:y };
}
// setting the viewport width
var viewport = updateViewportDimensions();


/*
 * Throttle Resize-triggered Events
 * Wrap your actions in this function to throttle the frequency of firing them off, for better performance, esp. on mobile.
 * ( source: http://stackoverflow.com/questions/2854407/javascript-jquery-window-resize-how-to-fire-after-the-resize-is-completed )
*/
var waitForFinalEvent = (function () {
	var timers = {};
	return function (callback, ms, uniqueId) {
		if (!uniqueId) { uniqueId = "Don't call this twice without a uniqueId"; }
		if (timers[uniqueId]) { clearTimeout (timers[uniqueId]); }
		timers[uniqueId] = setTimeout(callback, ms);
	};
})();

// how long to wait before deciding the resize has stopped, in ms. Around 50-100 should work ok.
var timeToWaitForLast = 100;


/*
 * Here's an example so you can see how we're using the above function
 *
 * This is commented out so it won't work, but you can copy it and
 * remove the comments.
 *
 *
 *
 * If we want to only do it on a certain page, we can setup checks so we do it
 * as efficient as possible.
 *
 * if( typeof is_home === "undefined" ) var is_home = $('body').hasClass('home');
 *
 * This once checks to see if you're on the home page based on the body class
 * We can then use that check to perform actions on the home page only
 *
 * When the window is resized, we perform this function
 * $(window).resize(function () {
 *
 *    // if we're on the home page, we wait the set amount (in function above) then fire the function
 *    if( is_home ) { waitForFinalEvent( function() {
 *
 *	// update the viewport, in case the window size has changed
 *	viewport = updateViewportDimensions();
 *
 *      // if we're above or equal to 768 fire this off
 *      if( viewport.width >= 768 ) {
 *        console.log('On home page and window sized to 768 width or more.');
 *      } else {
 *        // otherwise, let's do this instead
 *        console.log('Not on home page, or window sized to less than 768.');
 *      }
 *
 *    }, timeToWaitForLast, "your-function-identifier-string"); }
 * });
 *
 * Pretty cool huh? You can create functions like this to conditionally load
 * content and other stuff dependent on the viewport.
 * Remember that mobile devices and javascript aren't the best of friends.
 * Keep it light and always make sure the larger viewports are doing the heavy lifting.
 *
*/

/*
 * We're going to swap out the gravatars.
 * In the functions.php file, you can see we're not loading the gravatar
 * images on mobile to save bandwidth. Once we hit an acceptable viewport
 * then we can swap out those images since they are located in a data attribute.
*/
function loadGravatars() {
  // set the viewport using the function above
  viewport = updateViewportDimensions();
  // if the viewport is tablet or larger, we load in the gravatars
  if (viewport.width >= 768) {
  jQuery('.comment img[data-gravatar]').each(function(){
    jQuery(this).attr('src',jQuery(this).attr('data-gravatar'));
  });
	}
} // end function


/*
 * Put all your regular jQuery in here.
*/
jQuery(document).ready(function($) {

  /*
   * Smooth Scrolling
  */
  $('a[href*="#"]')
  // Remove links that don't actually link to anything
  .not('[href="#"]')
  .not('[href="#0"]')
  .not('[data-featherlight]')
  .not('.mobile-nav ul li.menu-item-has-children a')
  .click(function(event) {
    // On-page links
    if (
      location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
      && 
      location.hostname == this.hostname
    ) {
      // Figure out element to scroll to
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      // Does a scroll target exist?
      if (target.length) {
        // Only prevent default if animation is actually gonna happen
        event.preventDefault();
        var headerHeight = $('.main-nav-wrapper').outerHeight();
        var finalTarget = target.offset().top - headerHeight - 100;

        $('html, body').animate({
          scrollTop: finalTarget
        }, 1000);

        var finalTarget = 0;
      }
    }
  });


  /*
   *  Obfuscate email addresses.
  */
  $(function() {
   $('a[href^="mailto:"]').each(function() {
    this.href = this.href.replace('(at)', '@').replace(/\(dot\)/g, '.');
    // Remove this line if you don't want to set the email address as link text:
    this.innerHTML = this.href.replace('mailto:', '');
   });
  });

  /*
   * Sticky Navigation
  */
  var $el = $('.main-nav-wrapper');  //record the elem so you don't crawl the DOM everytime  
  var bottom = $el.position().top;

  if ($(window).scrollTop() > bottom) {
    $('header.site-header').addClass('scrolled');
  }

  $(window).bind('scroll', function() {
    var $el = $('.main-nav-wrapper');  //record the elem so you don't crawl the DOM everytime  
    var bottom = $el.position().top;

    if ($(window).scrollTop() > bottom) {
      $('header.site-header').addClass('scrolled');
    } else {
      $('header.site-header').removeClass('scrolled');
    }
  });

  /*
   * Submenus (for the main menu)
  */
  $('.main-nav > li.menu-item-has-children > a').on('click', function(e) {
      e.preventDefault();
      $(this).parent().toggleClass('open');
      $(this).parent().siblings().removeClass('open');
  })

  /*
   * Mobile Navigation
  */

  // 1. Make a copy of the navigation at the end of the document
  $('nav.main-nav').clone().removeClass('main-nav').addClass('mobile-nav').appendTo('body').children('ul.main-nav').removeClass('main-nav').addClass('mobile-nav');
  $('.top-nav .masthead-links').clone().appendTo('nav.mobile-nav');

  // 2. Open the mobile nav when needed
  $('.mobile-nav-trigger').on('click', function() {
    $(this).toggleClass('close');
    $(this).children('i').toggleClass('fa-bars').toggleClass('fa-times');
    if(!$('header.site-header').hasClass('scrolled') || $(window).scrollTop() == 0) {
      $('header.site-header').toggleClass('scrolled');
    }
    $('nav.mobile-nav').toggleClass('open');
  });

  // 2. Open the submenus when needed
  $('ul.mobile-nav > li.menu-item-has-children').on('click', function(e) {
    e.preventDefault();
    if($(this).children('.sub-menu').children('.mobile-nav-back').length === 0) {
      $(this).children('.sub-menu').prepend('<a class="mobile-nav-back"><i class="fas fa-chevron-left"></i> Back</a>');
    }
    $(this).toggleClass('open');
  });

  $('.mobile-nav-back').on('click', function(e) {
    e.preventDefault();
   // $(this).parent().toggleClass('open');
    $(this).remove();
  });

   $('ul.mobile-nav > li.menu-item-has-children .sub-menu li.menu-item').on('click', function(e) {
    e.preventDefault();
   });

   $('ul.mobile-nav .sub-menu .menu-item > a').click(function() {
      window.location = $(this).attr('href');
  });

  /*
   * Home Page Tabs
  */
  // Fix the height of the slides
  linkHeight = $('.home-hero-tabs .links').outerHeight();
  tabHeight = -1;
  $('.home-hero-tabs .slides .slide .slide-content').each(function() {
    tabHeight = tabHeight > $(this).outerHeight() ? tabHeight : $(this).outerHeight();
  });

  if(tabHeight < linkHeight) {
    $('.home-hero-tabs .slides').css('min-height', 'calc(' + linkHeight + 'px + 4em)' );
    $('.home-hero-tabs .slides .slide .slide-content').css('min-height', 'calc(' + linkHeight + 'px + 4em)' );
  } else {
    $('.home-hero-tabs .slides').css('min-height', 'calc(' + tabHeight + 'px + 4em)' );
    $('.home-hero-tabs .slides .slide .slide-content').css('min-height', 'calc(' + tabHeight + 'px + 4em)' );
  }

  // Make the tabs work
  $('.home-hero-tabs .links .link').not('.link-heading').on('click', function() {
    target = $(this).data('target');

    $(this).siblings().removeClass('active');
    $(this).addClass('active');
    $('.home-hero-tabs .slides .slide').removeClass('active');
    $('.home-hero-tabs .slides .slide[data-content="' + target + '"]').addClass('active');
  });

  // Woocommerce make customers agree to the proof warning.
  if( $('.woocommerce-checkout #content .proof-warnging').length === 0 ) {
    
  }

  /*
   * Let's fire off the gravatar function
   * You can remove this if you don't need it
  */
  loadGravatars();


}); /* end of as page load scripts */

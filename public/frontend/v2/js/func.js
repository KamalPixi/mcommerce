//Nested Dropdown Function
$('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    if (!$(this).next().hasClass('show')) {
      $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
    }
    var $subMenu = $(this).next(".dropdown-menu");
    $subMenu.toggleClass('show');


    $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
      $('.dropdown-submenu .show').removeClass("show");
    });


    return false;
  });

//Owl carousel Function
$(document).ready(function(){
    $('.trendingSlider').owlCarousel({
        loop:false,
        margin:10,
        dots:false,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
                nav:false
            },
            600:{
                items:3,
                nav:false
            },
            1000:{
                items:4,
                nav:false,
                loop:false
            }
        }
    });

    let trendingSlider = $('.trendingSlider');
    trendingSlider.owlCarousel();
    // Go to the next item
    $('.trendingSliderPrev').click(function() {
        trendingSlider.trigger('next.owl.carousel');
    });
    // Go to the previous item
    $('.trendingSliderNext').click(function() {
        // With optional speed parameter
        // Parameters has to be in square bracket '[]'
        trendingSlider.trigger('prev.owl.carousel', [300]);
    });

  });



  $(document).ready(function(){
    "use strict";

    $('.category-section__owl').owlCarousel({
        loop:true,
        margin:0,
        responsiveClass:true,
        dots:false,
        responsive:{
            0:{
                items:2,
                nav:true,
            },
            600:{
                items:5,
                nav:false,
            },
            1000:{
                items:10,
                nav:true,
                loop:true,
            }
        }
    });
    $('.offer-section__owl').owlCarousel({
        loop:true,
        margin:10,
        responsiveClass:true,
        dots:false,
        responsive:{
            0:{
                items:2,
                nav:true
            },
            600:{
                items:3,
                nav:false,
            },
            1000:{
                items:4,
                nav:true,
                loop:true,
            }
        }
    });

    $('.offer-owl-carousel').owlCarousel({
        stagePadding: 50,
        loop:true,
        margin:10,
        nav:true,
        dots:false,
        responsive:{
            0:{
                items:1,
            },
            600:{
                items:2,
            },
            1000:{
                items:5,
            }
        }
    });

    $('.owl-carousel').owlCarousel({
        loop:true,
        margin:10,
        responsiveClass:true,
        dots:false,
        responsive:{
            0:{
                items:1,
                nav:true
            },
            600:{
                items:1,
                nav:false,
            },
            1000:{
                items:1,
                nav:true,
                loop:true,
            }
        }
    });
  });



  // bootnavbar
  (function($) {
      var defaults={
          sm : 540,
          md : 720,
          lg : 960,
          xl : 1140,
          navbar_expand: 'lg',
          animation: true,
          animateIn: 'fadeIn',
      };
      $.fn.bootnavbar = function(options) {

          var screen_width = $(document).width();
          settings = $.extend(defaults, options);

          if(screen_width >= settings.lg){
              $(this).find('.dropdown').hover(function() {
                  $(this).addClass('show');
                  $(this).find('.dropdown-menu').first().addClass('show');
                  if(settings.animation){
                      $(this).find('.dropdown-menu').first().addClass('animated ' + settings.animateIn);
                  }
              }, function() {
                  $(this).removeClass('show');
                  $(this).find('.dropdown-menu').first().removeClass('show');
              });
          }

          $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
            if (!$(this).next().hasClass('show')) {
              $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
            }
            var $subMenu = $(this).next(".dropdown-menu");
            $subMenu.toggleClass('show');

            $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
              $('.dropdown-submenu .show').removeClass("show");
            });

            return false;
          });
      };
  })(jQuery);

  const observer = lozad(); // lazy loads elements with default selector as '.lozad'
  observer.observe();

  $(document).on('click', '.widget_wraper .dropdown-menu', function (e) {
    e.stopPropagation();
  });

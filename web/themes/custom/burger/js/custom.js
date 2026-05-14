(function ($, Drupal, once) {

  // Polyfill for jQuery 4.0 compatibility (needed by Owl Carousel 2)
  if (typeof $.camelCase !== 'function') {
    $.camelCase = function (str) {
      return str.replace(/-([a-z])/g, function (g) { return g[1].toUpperCase(); });
    };
  }

  // Dummy myMap function to prevent Google Maps callback errors
  window.myMap = window.myMap || function () {
    console.log("Google Maps API callback executed.");
  };

  Drupal.behaviors.myCustomScripts = {
    attach: function (context) {
      // Hide exposed form for both page and block displays
      $("form[id^='views-exposed-form-burger-menu-page']").hide();

      // =============================
      // Get current year
      // =============================
      once('year-set', context.querySelectorAll('#displayYear')).forEach(function (element) {
        element.innerHTML = new Date().getFullYear();
      });


      // =============================
      // Isotope JS
      // =============================
      once('isotope-init', context.querySelectorAll('.grid')).forEach(function (element) {

        var $grid = $(element).isotope({
          itemSelector: ".all",
          percentPosition: false,
          masonry: {
            columnWidth: ".all"
          }
        });

        // Filter click
        $(once('filter-click', '.filters_menu li', context)).each(function () {
          $(this).on('click', function (e) {
            e.preventDefault();

            $('.filters_menu li').removeClass('active');
            $(this).addClass('active');

            var data = $(this).attr('data-filter');
            var burger_type = $(this).attr('data-value') || 'all';

            // Find the Drupal views filter dropdown
            // Note: Drupal IDs sometimes vary, so we try multiple common patterns
            var $form = $("form[id^='views-exposed-form-burger-menu-page']");
            var $dropdown = $form.find('select[name="field_menu_types_value"]');

            if ($dropdown.length) {
              // Set the value in the hidden dropdown
              $dropdown.val(burger_type);

              // Force the form action to the current page to prevent redirecting to /burger-menu-page
              $form.attr('action', window.location.pathname);

              // Trigger the AJAX submit
              var $submit = $form.find('input[type="submit"], button[type="submit"]');
              if ($submit.length) {
                // We use mousedown + click as Drupal AJAX often listens for mousedown
                $submit.trigger('mousedown').click();
              }
            }

            // Isotope manual filter for immediate UI feedback
            if ($grid.length) {
              $grid.isotope({
                filter: data
              });
            }
          });
        });

      });


      // =============================
      // Nice Select
      // =============================
      once('nice-select', context.querySelectorAll('select')).forEach(function (element) {
        $(element).niceSelect();
      });


      // =============================
      // Owl Carousel
      // =============================
      once('owl-init', context.querySelectorAll('.client_owl-carousel')).forEach(function (element) {
        var $carousel = $(element);
        var itemCount = $carousel.children().length;

        if (itemCount > 0) {
          $carousel.owlCarousel({
            loop: itemCount > 1,
            margin: 0,
            dots: false,
            nav: true,
            autoplay: true,
            autoplayHoverPause: true,
            smartSpeed: 800,
            navText: [
              '<i class="fa fa-angle-left"></i>',
              '<i class="fa fa-angle-right"></i>'
            ],
            responsive: {
              0: { items: 1 },
              768: { items: 2 },
              1000: { items: 2 }
            }
          });
        } else {
          console.warn("Owl Carousel found but has no slides.");
          $carousel.hide(); // Hide if empty to avoid layout shifts or errors
        }

      });

    }
  };

})(jQuery, Drupal, once);
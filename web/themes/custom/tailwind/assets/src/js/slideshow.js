(function (Drupal, once) {
  Drupal.behaviors.swiper = {
    attach: function (context, settings) {
      once("banner-slideshow", ".swiper", context).forEach((el) => {
        var swiper = new Swiper(".mySwiper", {
          // Optional parameters
          direction: "horizontal",
          loop: true,

          // If we need pagination
          pagination: {
            el: ".swiper-pagination",
            dynamicBullets: true
          },

          // Navigation arrows
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },

          // And if we need scrollbar
          scrollbar: {
            el: ".swiper-scrollbar",
          },
        });
      });
    },
  };

  // The parameters are reversed in the callback between jQuery `.each` method
  // and the native `.forEach` array method.
})(Drupal, once);

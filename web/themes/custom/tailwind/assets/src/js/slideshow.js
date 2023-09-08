(function (Drupal, once) {
  Drupal.behaviors.swiper = {
    attach(context) {
      const swiper = new Swiper(".swiper", {
        // Optional parameters
        direction: "vertical",
        loop: true,

        // If we need pagination
        pagination: {
          el: ".swiper-pagination",
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

      console.log("Firing");
    },
  };

  // The parameters are reversed in the callback between jQuery `.each` method
  // and the native `.forEach` array method.
})(Drupal, once);

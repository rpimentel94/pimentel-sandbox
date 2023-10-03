(function (Drupal, drupalSettings, once) {
  "use strict";

  Drupal.behaviors.alertMessages = {
    attach: function (context, settings) {
      once("site-alert-item", "div.site-alert-item", context).forEach(function (
        element
      ) {
        // Apply the alertMessages effect to the elements only once.
        if (localStorage.getItem(element.id)) {
          element.remove();
        } else {
          element.classList.remove("hidden");
        }

        const dismissAlertButton = document.querySelector(
          ".site-alert-item button"
        );

        if (localStorage.getItem(element.id)) {
          element.remove();
        }

        if (dismissAlertButton) {
          dismissAlertButton.addEventListener("click", (event) => {
            event.preventDefault();
            localStorage.setItem(element.id, true);
            element.style.transition = "1s";
            element.style.opacity = "0";
          });

          element.addEventListener("transitionend", (event) => {
            element.remove();
          });
        }
      });
    },
  };
})(Drupal, drupalSettings, once);

(function (Drupal, drupalSettings, once) {
  "use strict";

  Drupal.behaviors.onlineBanking = {
    attach: function (context, settings) {
      document
        .getElementById("login-type")
        .addEventListener("change", function () {
          "use strict";
          var vis = document.querySelector(".vis"),
            target = document.getElementById(this.value);
          if (vis !== null) {
            vis.className = "inv";
          }
          if (target !== null) {
            target.className = "vis";
          }
        });
    },
  };
})(Drupal, drupalSettings, once);

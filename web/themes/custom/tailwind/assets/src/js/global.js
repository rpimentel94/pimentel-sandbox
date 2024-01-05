(function (Drupal, drupalSettings, once) {
  "use strict";

  Drupal.behaviors.globalFuntions = {
    attach: function (context, settings) {

      once("table", "article table", context).forEach((el) => {
        wrap(el);
      });

      once("a", ".js-social-share a", context).forEach((el) => {
        appendUrl(el);
      });

      once('span', '.search-modal-open span', context).forEach(el => {
        el.addEventListener('click', () => {
          toggleModal();
        });
      });

      once('svg', '.modal-close svg', context).forEach(el => {
        el.addEventListener('click', () => {
          toggleModal();
        });
      });

      once('button', '.search-form-submit button', context).forEach(el => {
        el.addEventListener('click', () => {
          var searchTerm = document.getElementById("search-term").value;
          //console.log("/search?keyword=" + searchTerm);
          window.location.assign("/search?keyword=" + searchTerm);

        });
      });

      function wrap(el) {
        const wrapper = document.createElement("div");
        wrapper.classList.add("tailwind-table");
        el.parentNode.insertBefore(wrapper, el);
        wrapper.appendChild(el);
      }

      function appendUrl(el) {
        let href = el.href;
        el.href = href + window.location;
      }


      document.onkeydown = function (evt) {
        evt = evt || window.event;
        var isEscape = false;
        if ("key" in evt) {
          isEscape = evt.key === "Escape" || evt.key === "Esc";
        } else {
          isEscape = evt.keyCode === 27;
        }
        if (isEscape && document.body.classList.contains("modal-active")) {
          toggleModal();
        }
      };

      function toggleModal() {
        const body = document.querySelector("body");
        const modal = document.querySelector(".modal");
        modal.classList.toggle("opacity-0");
        modal.classList.toggle("pointer-events-none");
        body.classList.toggle("modal-active");
      }
    },
  };
})(Drupal, drupalSettings, once);

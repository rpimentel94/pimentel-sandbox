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

        function wrap(el) {
            const wrapper = document.createElement('div');
            wrapper.classList.add('tailwind-table');
            el.parentNode.insertBefore(wrapper, el);
            wrapper.appendChild(el);
        }

        function appendUrl(el) {
         let href = el.href;
         el.href = href + window.location;
        }

        function toggleSearchModal() { document.getElementById('modal').classList.toggle('hidden')
        }

      },
    };
  })(Drupal, drupalSettings, once);
  
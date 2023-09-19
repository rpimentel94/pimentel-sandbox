(function (Drupal, once) {
  Drupal.behaviors.tabs = {
    attach: function (context, settings) {
      let tabsContainer = document.querySelector("#tabs");

      let tabTogglers = tabsContainer.querySelectorAll("#tabs a");

      tabTogglers.forEach(function (toggler) {
        toggler.addEventListener("click", function (e) {
          e.preventDefault();

          let tabName = this.getAttribute("href");

          let tabContents = document.querySelector("#tab-contents");

          for (let i = 0; i < tabContents.children.length; i++) {
            tabTogglers[i].parentElement.classList.remove(
              "border-t",
              "border-r",
              "border-l",
              "-mb-px",
              "bg-white"
            );
            tabContents.children[i].classList.remove("hidden");
            if ("#" + tabContents.children[i].id === tabName) {
              continue;
            }
            tabContents.children[i].classList.add("hidden");
          }
          e.target.parentElement.classList.add(
            "border-t",
            "border-r",
            "border-l",
            "-mb-px",
            "bg-white"
          );
        });
      });
    },
  };

  // The parameters are reversed in the callback between jQuery `.each` method
  // and the native `.forEach` array method.
})(Drupal, once);

(function (Drupal, once) {
  Drupal.behaviors.vTabs = {
    attach: function (context, settings) {

      let tabsContainer = document.querySelector("#v-tabs");

      let tabTogglers = tabsContainer.querySelectorAll("#v-tabs a");

      tabTogglers.forEach(function (toggler) {
        toggler.addEventListener("click", function (e) {
          e.preventDefault();

          let tabName = this.getAttribute("href");

          let tabContents = document.querySelector("#v-tab-contents");

          for (let i = 0; i < tabContents.children.length; i++) {
            tabTogglers[i].parentElement.classList.remove(
              "text-white",
              "bg-[--PrimaryColor]"
            );
            tabContents.children[i].classList.remove("hidden");
            if ("#" + tabContents.children[i].id === tabName) {
              continue;
            }
            tabContents.children[i].classList.add("hidden");
          }
          e.target.parentElement.classList.add(
            "text-white",
            "bg-[--PrimaryColor]"
          );
        });
      });
    },
  };

  // The parameters are reversed in the callback between jQuery `.each` method
  // and the native `.forEach` array method.
})(Drupal, once);

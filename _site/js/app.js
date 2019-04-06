var app = (function() {
  "use strict";

  var loadImages = function() {
    var images = document.querySelectorAll("img.images-lazy");

    for (var i = 0; i < images.length; i++) {
      var el = images[i];
      var src = images[i].getAttribute("data-src");

      el.onload = function() {
        this.classList.remove("hide");
        var loader = this.parentNode.querySelector("#loader");

        if (loader != null) {
          loader.classList.add("hide");
        }
      };

      el.setAttribute("src", src);
    }
  };

  var initialize = function() {
    loadImages();
  };

  return { initialize: initialize };
})();

app.initialize();

(() => {
  const images = Array.from(document.querySelectorAll(".article__body img"));

  if (!images.length) return;

  const lightbox = document.createElement("div");
  lightbox.className = "image-lightbox";
  lightbox.setAttribute("role", "dialog");
  lightbox.setAttribute("aria-modal", "true");
  lightbox.setAttribute("aria-hidden", "true");

  const close = document.createElement("button");
  close.className = "image-lightbox__close";
  close.type = "button";
  close.setAttribute("aria-label", document.documentElement.lang === "en" ? "Close image" : "Fechar imagem");
  close.textContent = "x";

  const image = document.createElement("img");
  image.className = "image-lightbox__image";
  image.alt = "";

  lightbox.append(close, image);
  document.body.append(lightbox);

  function openImage(source) {
    image.src = source.currentSrc || source.src;
    image.alt = source.alt || "";
    lightbox.classList.add("image-lightbox--open");
    lightbox.setAttribute("aria-hidden", "false");
    document.body.classList.add("image-lightbox-open");
    close.focus();
  }

  function closeImage() {
    lightbox.classList.remove("image-lightbox--open");
    lightbox.setAttribute("aria-hidden", "true");
    document.body.classList.remove("image-lightbox-open");
    image.removeAttribute("src");
  }

  images.forEach((articleImage) => {
    if (articleImage.closest("a")) return;

    articleImage.classList.add("article__image--zoomable");
    articleImage.tabIndex = 0;
    articleImage.setAttribute("role", "button");
    articleImage.setAttribute("aria-label", document.documentElement.lang === "en" ? "Open image" : "Ampliar imagem");

    articleImage.addEventListener("click", () => openImage(articleImage));
    articleImage.addEventListener("keydown", (event) => {
      if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        openImage(articleImage);
      }
    });
  });

  close.addEventListener("click", closeImage);
  lightbox.addEventListener("click", (event) => {
    if (event.target === lightbox) closeImage();
  });
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && lightbox.classList.contains("image-lightbox--open")) {
      closeImage();
    }
  });
})();

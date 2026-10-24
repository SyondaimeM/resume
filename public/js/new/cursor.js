(() => {
  const dot = document.querySelector(".cursor-dot");
  const outline = document.querySelector(".cursor-dot-outline");
  const enabled = window.matchMedia("(min-width: 992px) and (hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)");

  if (!dot || !outline) {
    return;
  }

  function hide() {
    dot.style.opacity = "0";
    outline.style.opacity = "0";
  }

  document.addEventListener("pointermove", (event) => {
    if (!enabled.matches || event.pointerType !== "mouse") {
      hide();
      return;
    }

    for (const element of [dot, outline]) {
      element.style.left = event.clientX + "px";
      element.style.top = event.clientY + "px";
      element.style.opacity = "1";
    }
    const interactive = event.target instanceof Element && event.target.closest("a, button, input, textarea, select, label");
    outline.style.transform = `translate(-50%, -50%) scale(${interactive ? 1.5 : 1})`;
  });

  document.addEventListener("pointerleave", hide);
  window.addEventListener("blur", hide);
  document.addEventListener("scroll", hide, { passive: true });
  enabled.addEventListener("change", hide);
})();

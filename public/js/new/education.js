document.querySelectorAll(".timeline-content").forEach((element) => {
  element.setAttribute("data-aos", "fade-up");
  element.setAttribute("data-aos-duration", "800");
});

AOS.init();

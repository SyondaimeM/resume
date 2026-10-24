(() => {
  let lastReport = 0;

  function report(details) {
    if (Date.now() - lastReport < 60000) return;
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) return;
    lastReport = Date.now();
    fetch("/client-errors", {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/json", "Accept": "application/json", "X-CSRF-TOKEN": token.content },
      body: JSON.stringify(details),
      keepalive: true,
    }).then((response) => {
      if (!response.ok) console.error("The browser error could not be reported:", response.status);
    }).catch(() => console.error("The browser error could not be reported: network unavailable."));
  }

  window.addEventListener("error", (event) => {
    if (!event.filename) return;
    const source = new URL(event.filename, location.href);
    if (source.origin !== location.origin) return;
    report({ kind: "script", source: source.pathname.split("/").pop(), line: event.lineno });
  });
  window.addEventListener("unhandledrejection", () => report({ kind: "promise" }));
})();

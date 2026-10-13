(() => {
  "use strict";
  const year = document.getElementById("year");
  if (year) year.textContent = String(new Date().getFullYear());

  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const revealItems = document.querySelectorAll("[data-reveal]");
  if (reducedMotion || !("IntersectionObserver" in window)) {
    revealItems.forEach((item) => item.classList.add("is-visible"));
  } else {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: "0px 0px -25px 0px" });
    revealItems.forEach((item) => observer.observe(item));
  }

  const progress = document.getElementById("reading-progress");
  let queued = false;
  const updateProgress = () => {
    const max = document.documentElement.scrollHeight - window.innerHeight;
    const value = max > 0 ? Math.min(100, Math.max(0, window.scrollY / max * 100)) : 0;
    if (progress) progress.style.width = value + "%";
    queued = false;
  };
  window.addEventListener("scroll", () => {
    if (!queued) {
      window.requestAnimationFrame(updateProgress);
      queued = true;
    }
  }, { passive: true });
  window.addEventListener("resize", updateProgress, { passive: true });
  updateProgress();

  const form = document.getElementById("interest-form");
  if (!form) return;
  const status = document.getElementById("form-status");
  const fallback = document.getElementById("form-fallback");
  const submit = form.querySelector('button[type="submit"]');
  const setStatus = (message, error = false) => {
    status.textContent = message;
    status.classList.toggle("is-error", error);
  };
  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (fallback) fallback.hidden = true;
    setStatus("");
    if (!form.reportValidity()) {
      setStatus("Please complete the required fields and check your email address.", true);
      return;
    }
    if (form.querySelector('input[name="_honey"]').value) {
      setStatus("Your message could not be processed.", true);
      return;
    }
    if (!navigator.onLine) {
      setStatus("You appear to be offline. Reconnect and try again, or use the direct email option below.", true);
      if (fallback) fallback.hidden = false;
      return;
    }
    const originalText = submit.innerHTML;
    submit.disabled = true;
    submit.setAttribute("aria-busy", "true");
    submit.textContent = "Sending your note…";
    setStatus("Sending your enquiry over HTTPS.");
    const controller = new AbortController();
    let timedOut = false;
    const timeout = window.setTimeout(() => { timedOut = true; controller.abort(); }, 15000);
    try {
      const response = await fetch("https://formsubmit.co/ajax/rosscorelabs@outlook.com", {
        method: "POST",
        body: new FormData(form),
        headers: { Accept: "application/json" },
        signal: controller.signal
      });
      const result = await response.json().catch(() => { throw new Error("Invalid response from enquiry service"); });
      if (!response.ok || String(result.success) !== "true") throw new Error("Delivery was not confirmed");
      form.reset();
      setStatus("Thanks — your note was submitted. Rosscore Labs can follow up using the email you provided.");
      if (fallback) fallback.hidden = true;
    } catch (error) {
      console.error("SwiftOrder enquiry submission failed:", error instanceof Error ? error.name : "UnknownError");
      setStatus(timedOut ? "The request timed out; delivery could not be confirmed." : "We couldn’t confirm delivery of your note.", true);
      if (fallback) fallback.hidden = false;
    } finally {
      window.clearTimeout(timeout);
      submit.disabled = false;
      submit.removeAttribute("aria-busy");
      submit.innerHTML = originalText;
    }
  });
})();
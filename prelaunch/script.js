(() => {
  "use strict";
  const year = document.getElementById("year");
  if (year) year.textContent = String(new Date().getFullYear());

  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const revealItems = document.querySelectorAll("[data-reveal]");
  if (reducedMotion || !("IntersectionObserver" in window)) {
    revealItems.forEach((item) => item.classList.add("is-visible"));
  } else {
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -25px 0px" });
    revealItems.forEach((item) => revealObserver.observe(item));
  }

  const progress = document.getElementById("scroll-progress-fill");
  let scrollQueued = false;
  const updateProgress = () => {
    if (!progress) return;
    const max = document.documentElement.scrollHeight - window.innerHeight;
    const percent = max > 0 ? Math.min(100, Math.max(0, window.scrollY / max * 100)) : 0;
    progress.style.width = percent + "%";
    scrollQueued = false;
  };
  window.addEventListener("scroll", () => {
    if (!scrollQueued) {
      window.requestAnimationFrame(updateProgress);
      scrollQueued = true;
    }
  }, { passive: true });
  window.addEventListener("resize", updateProgress, { passive: true });
  updateProgress();

  const navigator = document.querySelector("[data-section-navigator]");
  const current = document.getElementById("section-current");
  const next = document.querySelector("[data-section-next]");
  const sections = Array.from(document.querySelectorAll("main section[id]"));
  const names = new Map(sections.map((section) => {
    const known = { top: "Introduction", principles: "Product principles", workflow: "How it works", "who-its-for": "Who it helps", "early-access": "Early access" };
    const heading = section.querySelector("h1,h2,h3");
    return [section.id, known[section.id] || (heading?.textContent || section.id).trim().replace(/\s+/g, " ")];
  }));
  let activeId = sections[0]?.id || "top";
  let hideTimer = 0;
  let scrollFrame = false;
  let targetId = null;
  let targetTimer = 0;
  const showNavigator = () => {
    if (!navigator) return;
    navigator.classList.add("is-visible");
    window.clearTimeout(hideTimer);
    hideTimer = window.setTimeout(() => {
      if (!navigator.matches(":hover,:focus-within")) navigator.classList.remove("is-visible");
    }, 1900);
  };
  const updateNavigator = (id) => {
    const index = sections.findIndex((section) => section.id === id);
    if (index < 0) return;
    activeId = id;
    if (current) current.textContent = names.get(id) || "Explore";
    const following = sections[(index + 1) % sections.length];
    if (next && following) {
      const atEnd = index === sections.length - 1;
      next.href = "#" + following.id;
      next.setAttribute("aria-label", atEnd ? "Return to the introduction" : "Next section: " + (names.get(following.id) || "next section"));
      next.innerHTML = (atEnd ? "Back to introduction " : "Next section ") + '<span aria-hidden="true">↓</span>';
    }
  };
  const detectSection = () => {
    scrollFrame = false;
    if (!sections.length) return;
    const marker = Math.min(window.innerHeight * 0.38, 285);
    if (targetId) {
      const target = sections.find((section) => section.id === targetId);
      if (target && target.getBoundingClientRect().top > marker) {
        updateNavigator(targetId);
        return;
      }
      targetId = null;
      window.clearTimeout(targetTimer);
    }
    let candidate = sections[0];
    sections.forEach((section) => {
      if (section.getBoundingClientRect().top <= marker) candidate = section;
    });
    updateNavigator(candidate.id);
  };
  if (navigator && sections.length) {
    const initialHash = decodeURIComponentSafe(window.location.hash.slice(1));
    updateNavigator(sections.some((section) => section.id === initialHash) ? initialHash : activeId);
    window.addEventListener("scroll", () => {
      if (!scrollFrame) {
        window.requestAnimationFrame(detectSection);
        scrollFrame = true;
      }
      showNavigator();
    }, { passive: true });
    window.addEventListener("pointermove", (event) => {
      if (event.clientX > window.innerWidth - 175 && Math.abs(event.clientY - window.innerHeight / 2) < 130) showNavigator();
    }, { passive: true });
    navigator.addEventListener("focusin", showNavigator);
    navigator.addEventListener("mouseenter", showNavigator);
    if (next) next.addEventListener("click", (event) => {
      const index = sections.findIndex((section) => section.id === activeId);
      if (index < 0) return;
      event.preventDefault();
      const destination = sections[(index + 1) % sections.length];
      targetId = destination.id;
      updateNavigator(targetId);
      showNavigator();
      window.clearTimeout(targetTimer);
      targetTimer = window.setTimeout(() => { targetId = null; detectSection(); }, 2800);
      const hash = "#" + destination.id;
      if (window.location.hash !== hash) window.history.pushState(null, "", hash);
      const heading = destination.querySelector("h1,h2,h3") || destination;
      heading.scrollIntoView({ behavior: reducedMotion ? "auto" : "smooth", block: "start" });
    });
    window.addEventListener("hashchange", () => {
      const id = decodeURIComponentSafe(window.location.hash.slice(1));
      if (sections.some((section) => section.id === id)) {
        targetId = id;
        updateNavigator(id);
        showNavigator();
        window.clearTimeout(targetTimer);
        targetTimer = window.setTimeout(() => { targetId = null; detectSection(); }, 2500);
      }
    });
  }
  function decodeURIComponentSafe(value) {
    try { return decodeURIComponent(value); } catch { return value; }
  }

  const form = document.getElementById("interest-form");
  if (!form) return;
  const status = document.getElementById("form-status");
  const fallback = document.getElementById("form-fallback");
  const submit = form.querySelector('button[type="submit"]');
  const email = form.querySelector('input[name="email"]');
  const name = form.querySelector('input[name="name"]');
  const setStatus = (message, error = false) => {
    if (!status) return;
    status.textContent = message;
    status.classList.toggle("is-error", error);
  };
  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (fallback) fallback.hidden = true;
    setStatus("");
    if (!name.value.trim() || !email.value.trim() || !email.validity.valid || !form.reportValidity()) {
      setStatus("Please check the required fields and enter a valid email address.", true);
      return;
    }
    if (form.querySelector('input[name="_honey"]').value) {
      setStatus("Thanks. Your message could not be processed.", true);
      return;
    }
    if (!navigator.onLine) {
      setStatus("You appear to be offline. Connect and try again, or use the direct email link below.", true);
      if (fallback) fallback.hidden = false;
      return;
    }
    const originalText = submit.innerHTML;
    submit.disabled = true;
    submit.setAttribute("aria-busy", "true");
    submit.textContent = "Sending enquiry…";
    setStatus("Sending your enquiry securely over HTTPS.");
    const controller = new AbortController();
    let requestTimedOut = false;
    const timeout = window.setTimeout(() => { requestTimedOut = true; controller.abort(); }, 15000);
    try {
      const response = await fetch("https://formsubmit.co/ajax/rosscorelabs@outlook.com", {
        method: "POST",
        body: new FormData(form),
        headers: { Accept: "application/json" },
        signal: controller.signal
      });
      const result = await response.json().catch(() => ({}));
      if (!response.ok || String(result.success) !== "true") throw new Error("The enquiry service did not confirm receipt.");
      form.reset();
      setStatus("Thanks — your enquiry was submitted. If you requested early access, Rosscore Labs can follow up using the email you provided.");
      if (fallback) fallback.hidden = true;
    } catch (error) {
      const message = requestTimedOut ? "The request timed out. Your enquiry may not have been sent." : "We couldn’t confirm delivery of your enquiry.";
      setStatus(message + " Please use the direct email fallback below.", true);
      if (fallback) fallback.hidden = false;
    } finally {
      window.clearTimeout(timeout);
      submit.disabled = false;
      submit.removeAttribute("aria-busy");
      submit.innerHTML = originalText;
    }
  });
})();
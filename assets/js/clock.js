function updateClock() {

    const el = document.getElementById("system-clock");

    if (!el) return;

    const now = new Date();

    const time = now.toLocaleTimeString("en-ZA", {
        hour12: false
    });

    el.textContent = time;
}

updateClock();
setInterval(updateClock, 1000);
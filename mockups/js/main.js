(function () {
  const end = new Date(
    Date.now() + 3 * 24 * 60 * 60 * 1000 + 14 * 60 * 60 * 1000,
  );

  function pad(n) {
    return String(n).padStart(2, "0");
  }

  function tick() {
    const diff = end - Date.now();
    if (diff <= 0) return;

    const days = Math.floor(diff / 86400000);
    const hours = Math.floor((diff % 86400000) / 3600000);
    const mins = Math.floor((diff % 3600000) / 60000);
    const secs = Math.floor((diff % 60000) / 1000);

    const d = document.getElementById("timer-days");
    const h = document.getElementById("timer-hours");
    const m = document.getElementById("timer-mins");
    const s = document.getElementById("timer-secs");

    if (d) d.textContent = pad(days);
    if (h) h.textContent = pad(hours);
    if (m) m.textContent = pad(mins);
    if (s) s.textContent = pad(secs);
  }

  tick();
  setInterval(tick, 1000);
})();
document.addEventListener("click", function (e) {
  const btn = e.target.closest(".btn-add-cart");
  if (!btn) return;

  const icon = btn.querySelector("i");
  if (!icon) return;

  icon.className = "bi bi-check-lg";
  btn.style.background = "var(--clr-gold)";
  btn.style.color = "var(--clr-bg)";
  btn.style.borderColor = "var(--clr-gold)";

  setTimeout(function () {
    icon.className = "bi bi-bag-plus";
    btn.style.background = "";
    btn.style.color = "";
    btn.style.borderColor = "";
  }, 1400);
});
document.addEventListener("click", function (e) {
  const btn = e.target.closest(".product-card__wishlist");
  if (!btn) return;

  const icon = btn.querySelector("i");
  if (!icon) return;

  const active = icon.classList.contains("bi-heart-fill");
  icon.className = active ? "bi bi-heart" : "bi bi-heart-fill";
  btn.style.color = active ? "" : "var(--clr-red-light)";
  btn.style.borderColor = active ? "" : "var(--clr-red)";
});
(function () {
  const slider = document.querySelector(".filter-price-slider");
  if (!slider) return;

  const rangeMin = document.getElementById("price-slider-min");
  const rangeMax = document.getElementById("price-slider-max");
  const inputMin = document.getElementById("min_price");
  const inputMax = document.getElementById("max_price");
  if (!rangeMin || !rangeMax || !inputMin || !inputMax) return;

  function syncToInputs() {
    inputMin.value = rangeMin.value;
    inputMax.value = rangeMax.value;
  }

  function syncToSliders() {
    var min = parseInt(inputMin.value, 10) || 0;
    var max = parseInt(inputMax.value, 10) || 10000;
    rangeMin.value = Math.min(min, parseInt(rangeMax.value, 10));
    rangeMax.value = Math.max(max, parseInt(rangeMin.value, 10));
    inputMin.value = rangeMin.value;
    inputMax.value = rangeMax.value;
  }

  rangeMin.addEventListener("input", function () {
    if (parseInt(rangeMin.value, 10) > parseInt(rangeMax.value, 10)) {
      rangeMax.value = rangeMin.value;
    }
    syncToInputs();
  });
  rangeMax.addEventListener("input", function () {
    if (parseInt(rangeMax.value, 10) < parseInt(rangeMin.value, 10)) {
      rangeMin.value = rangeMax.value;
    }
    syncToInputs();
  });
  inputMin.addEventListener("change", syncToSliders);
  inputMax.addEventListener("change", syncToSliders);

  function updateZIndex(e) {
    var rect = slider.getBoundingClientRect();
    var x = (e.clientX - rect.left) / rect.width;
    var minVal = parseInt(rangeMin.value, 10) / 10000;
    var maxVal = parseInt(rangeMax.value, 10) / 10000;
    var mid = (minVal + maxVal) / 2;
    rangeMin.style.zIndex = x < mid ? "3" : "1";
    rangeMax.style.zIndex = x >= mid ? "3" : "1";
  }
  slider.addEventListener("mousemove", updateZIndex);
  slider.addEventListener("mouseenter", updateZIndex);
})();
document.addEventListener("click", function (e) {
  const star = e.target.closest(".review-submit__star");
  if (!star) return;

  const container = star.closest(".review-submit__stars");
  if (!container) return;

  const rating = parseInt(star.dataset.rating, 10);
  const stars = container.querySelectorAll(".review-submit__star");
  const hidden = document.getElementById("review-rating-value");

  stars.forEach(function (s, i) {
    const icon = s.querySelector("i");
    if (!icon) return;
    if (i < rating) {
      icon.className = "bi bi-star-fill";
      s.classList.add("active");
    } else {
      icon.className = "bi bi-star";
      s.classList.remove("active");
    }
  });

  if (hidden) hidden.value = rating;
});

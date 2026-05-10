import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

(function () {
    var scrollKey = 'scrollY';
    var urlKey = 'scrollUrl';
    var saved = sessionStorage.getItem(scrollKey);
    var savedUrl = sessionStorage.getItem(urlKey);
    sessionStorage.removeItem(scrollKey);
    sessionStorage.removeItem(urlKey);
    if (saved !== null && savedUrl === location.pathname + location.search) {
        document.documentElement.style.scrollBehavior = 'auto';
        window.scrollTo(0, parseInt(saved, 10));
        document.documentElement.style.scrollBehavior = '';
    }
    document.addEventListener('submit', function () {
        sessionStorage.setItem(scrollKey, String(window.scrollY));
        sessionStorage.setItem(urlKey, location.pathname + location.search);
    });
})();

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

function updateCartBadge(count) {
    const badge = document.getElementById('cart-count-badge');
    if (!badge) return;
    badge.textContent = count;
    if (count > 0) {
        badge.removeAttribute('hidden');
    } else {
        badge.setAttribute('hidden', '');
    }
}

function showCartToast(message) {
    let toast = document.getElementById('cart-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'cart-toast';
        toast.className = 'cart-toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.add('cart-toast--show');
    clearTimeout(toast._hideTimer);
    toast._hideTimer = setTimeout(() => toast.classList.remove('cart-toast--show'), 2200);
}

document.addEventListener('submit', function (e) {
    const form = e.target.closest('form[data-cart-add]');
    if (!form) return;
    e.preventDefault();

    const formData = new FormData(form);
    axios.post(form.action, formData, {
        headers: { Accept: 'application/json' },
    })
        .then((res) => {
            updateCartBadge(res.data.count);
            showCartToast(res.data.message || 'Added to cart');
        })
        .catch(() => {
            // Fallback: native submit if AJAX fails for any reason.
            form.submit();
        });
});

(function () {
    const end = new Date(
        Date.now() + 3 * 24 * 60 * 60 * 1000 + 14 * 60 * 60 * 1000,
    );

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function tick() {
        const diff = end - Date.now();
        if (diff <= 0) return;

        const days = Math.floor(diff / 86400000);
        const hours = Math.floor((diff % 86400000) / 3600000);
        const mins = Math.floor((diff % 3600000) / 60000);
        const secs = Math.floor((diff % 60000) / 1000);

        const d = document.getElementById('timer-days');
        const h = document.getElementById('timer-hours');
        const m = document.getElementById('timer-mins');
        const s = document.getElementById('timer-secs');

        if (d) d.textContent = pad(days);
        if (h) h.textContent = pad(hours);
        if (m) m.textContent = pad(mins);
        if (s) s.textContent = pad(secs);
    }

    tick();
    setInterval(tick, 1000);
})();

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-add-cart');
    if (!btn) return;

    const icon = btn.querySelector('i');
    if (!icon) return;

    icon.className = 'bi bi-check-lg';
    btn.style.background = 'var(--clr-gold)';
    btn.style.color = 'var(--clr-bg)';
    btn.style.borderColor = 'var(--clr-gold)';

    setTimeout(function () {
        icon.className = 'bi bi-bag-plus';
        btn.style.background = '';
        btn.style.color = '';
        btn.style.borderColor = '';
    }, 1400);
});

document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-wishlist-toggle]');
    if (!btn) return;

    const productId = btn.dataset.wishlistToggle;
    const icon = btn.querySelector('i');
    if (!icon || !productId) return;

    axios.post(`/wishlist/toggle/${productId}`)
        .then(function (res) {
            const wishlisted = res.data.wishlisted;
            icon.className = wishlisted ? 'bi bi-heart-fill' : 'bi bi-heart';
            btn.style.color = wishlisted ? 'var(--clr-red-light)' : '';
            btn.style.borderColor = wishlisted ? 'var(--clr-red)' : '';
        })
        .catch(function () {});
});

(function () {
    const form = document.querySelector('[data-checkout-form]');
    if (!form) return;

    const shippingTarget = form.querySelector('[data-checkout-shipping]');
    const totalTarget = form.querySelector('[data-checkout-total]');
    if (!shippingTarget || !totalTarget) return;

    const subtotal = Number(form.dataset.subtotal || 0);
    const shippingCosts = JSON.parse(form.dataset.shippingCosts || '{}');

    function formatCrowns(value) {
        return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 })
            .format(value)
            .replace(/\u202f/g, ' ');
    }

    function updateCheckoutTotal() {
        const shippingId = form.querySelector('input[name="shipping_method_id"]:checked')?.value;
        const shippingCost = Number(shippingCosts[shippingId] || 0);

        shippingTarget.textContent = shippingCost === 0 ? 'Free' : `${formatCrowns(shippingCost)} Crowns`;
        totalTarget.textContent = `${formatCrowns(subtotal + shippingCost)} Crowns`;
    }

    form.addEventListener('change', function (e) {
        if (e.target.matches('input[name="shipping_method_id"], input[name="payment_method_id"]')) {
            updateCheckoutTotal();
        }
    });

    updateCheckoutTotal();
})();

(function () {
    const slider = document.querySelector('.filter-price-slider');
    if (!slider) return;

    const rangeMin = document.getElementById('price-slider-min');
    const rangeMax = document.getElementById('price-slider-max');
    const inputMin = document.getElementById('min_price');
    const inputMax = document.getElementById('max_price');
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

    rangeMin.addEventListener('input', function () {
        if (parseInt(rangeMin.value, 10) > parseInt(rangeMax.value, 10)) {
            rangeMax.value = rangeMin.value;
        }
        syncToInputs();
    });
    rangeMax.addEventListener('input', function () {
        if (parseInt(rangeMax.value, 10) < parseInt(rangeMin.value, 10)) {
            rangeMin.value = rangeMax.value;
        }
        syncToInputs();
    });
    inputMin.addEventListener('change', syncToSliders);
    inputMax.addEventListener('change', syncToSliders);

    function updateZIndex(e) {
        var rect = slider.getBoundingClientRect();
        var x = (e.clientX - rect.left) / rect.width;
        var minVal = parseInt(rangeMin.value, 10) / 10000;
        var maxVal = parseInt(rangeMax.value, 10) / 10000;
        var mid = (minVal + maxVal) / 2;
        rangeMin.style.zIndex = x < mid ? '3' : '1';
        rangeMax.style.zIndex = x >= mid ? '3' : '1';
    }
    slider.addEventListener('mousemove', updateZIndex);
    slider.addEventListener('mouseenter', updateZIndex);

    syncToSliders();
})();

document.addEventListener('click', function (e) {
    const star = e.target.closest('.review-submit__star');
    if (!star) return;

    const container = star.closest('.review-submit__stars');
    if (!container) return;

    const rating = parseInt(star.dataset.rating, 10);
    const stars = container.querySelectorAll('.review-submit__star');
    const hidden = document.getElementById('review-rating-value');

    stars.forEach(function (s, i) {
        const icon = s.querySelector('i');
        if (!icon) return;
        if (i < rating) {
            icon.className = 'bi bi-star-fill';
            s.classList.add('active');
        } else {
            icon.className = 'bi bi-star';
            s.classList.remove('active');
        }
    });

    if (hidden) hidden.value = rating;
});

document.addEventListener('click', function (e) {
    const thumb = e.target.closest('.product-gallery__thumb');
    if (!thumb) return;

    const thumbImg = thumb.querySelector('img');
    if (!thumbImg) return;

    const mainImg = document.querySelector('.product-gallery__img');
    if (!mainImg) return;

    mainImg.src = thumbImg.src;
    mainImg.alt = thumbImg.alt || mainImg.alt;

    thumb.closest('.product-gallery__thumbs')
        .querySelectorAll('.product-gallery__thumb')
        .forEach(function (t) { t.classList.remove('active'); });
    thumb.classList.add('active');
});

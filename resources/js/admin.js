'use strict';

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

(function () {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    const toggle = document.querySelector('.topbar-toggle');

    if (!sidebar) return;

    function open() {
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (toggle) toggle.addEventListener('click', open);
    if (overlay) overlay.addEventListener('click', close);
})();

document.querySelectorAll('.img-existing__rm').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var el = this.closest('.img-existing');
        if (el) el.remove();
    });
});

(function () {
    var grid = document.getElementById('image-grid');
    if (!grid) return;

    var uploadUrl = grid.dataset.uploadUrl;
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    grid.addEventListener('change', function (e) {
        var input = e.target;
        if (input.type !== 'file' || !input.files.length) return;

        var slot = input.closest('.img-slot');
        if (!slot) return;

        var file = input.files[0];

        if (uploadUrl && csrfToken) {
            slot.innerHTML = '<div class="img-slot__spinner"><i class="bi bi-arrow-repeat"></i></div>';
            slot.classList.add('img-slot--loading');

            var formData = new FormData();
            formData.append('image', file);

            fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData,
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var deleteUrl = uploadUrl + '/' + data.id;
                var formId = 'delete-image-' + data.id;

                var delForm = document.createElement('form');
                delForm.id = formId;
                delForm.action = deleteUrl;
                delForm.method = 'POST';
                delForm.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '" />' +
                    '<input type="hidden" name="_method" value="DELETE" />';
                document.body.appendChild(delForm);

                var div = document.createElement('div');
                div.className = 'img-existing';
                div.innerHTML =
                    '<img src="' + data.path + '" alt="" />' +
                    '<label class="img-existing__primary">' +
                        '<input type="radio" name="primary_image_id" value="' + data.id + '"' + (data.is_main ? ' checked' : '') + ' /> Primary' +
                    '</label>' +
                    '<button type="button" class="img-existing__rm" onclick="sessionStorage.setItem(\'scrollY\',String(window.scrollY));sessionStorage.setItem(\'scrollUrl\',location.pathname+location.search);document.getElementById(\'' + formId + '\').submit()"><i class="bi bi-x"></i></button>';
                slot.replaceWith(div);
            })
            .catch(function () {
                slot.classList.remove('img-slot--loading');
                slot.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Failed';
            });
        } else {
            var reader = new FileReader();
            reader.onload = function (ev) {
                slot.innerHTML = '<img src="' + ev.target.result + '" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-sm);" />';
                var newInput = document.createElement('input');
                newInput.type = 'file';
                newInput.name = 'images[]';
                newInput.accept = 'image/*';
                newInput.style.display = 'none';
                newInput.files = input.files;
                slot.appendChild(newInput);
            };
            reader.readAsDataURL(file);
        }
    });
})();

document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
        if (!confirm(this.dataset.confirm)) e.preventDefault();
    });
});

var statusSelect = document.getElementById('order-status-select');
var statusBadge = document.getElementById('current-status-badge');
var statusMap = {
    pending: ['badge-adm status-pending', 'Pending'],
    processing: ['badge-adm status-processing', 'Processing'],
    shipped: ['badge-adm status-shipped', 'Shipped'],
    delivered: ['badge-adm status-delivered', 'Delivered'],
    cancelled: ['badge-adm status-cancelled', 'Cancelled'],
};

if (statusSelect && statusBadge) {
    statusSelect.addEventListener('change', function () {
        var entry = statusMap[this.value] || ['badge-adm badge-grey', this.value];
        statusBadge.className = entry[0];
        statusBadge.textContent = entry[1];
    });
}

function showToast(msg, type) {
    var t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 18px;'
        + 'border-radius:6px;font-family:Cinzel,serif;font-size:.72rem;letter-spacing:.07em;'
        + 'text-transform:uppercase;font-weight:600;color:#fff;pointer-events:none;'
        + 'background:' + (type === 'error' ? '#8b1a1a' : '#2d7a45') + ';'
        + 'box-shadow:0 4px 16px rgba(0,0,0,.5);animation:toastIn .25s ease;';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function () { t.remove(); }, 3000);
}

window.showToast = showToast;

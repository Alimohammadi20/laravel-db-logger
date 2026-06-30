const faLang = {
    "emptyTable": "هیچ داده‌ای در جدول وجود ندارد",
    "info": "نمایش _START_ تا _END_ از _TOTAL_ ردیف",
    "infoEmpty": "نمایش 0 تا 0 از 0 ردیف",
    "infoFiltered": "(فیلتر شده از _MAX_ ردیف)",
    "infoThousands": ",",
    "lengthMenu": "نمایش _MENU_ ردیف",
    "processing": "در حال پردازش...",
    "search": "جستجو:",
    "zeroRecords": "رکوردی با این مشخصات پیدا نشد",
    "paginate": {"next": "بعدی", "previous": "قبلی", "first": "ابتدا", "last": "انتها"},
    "aria": {"sortAscending": ": فعال سازی نمایش به صورت صعودی", "sortDescending": ": فعال سازی نمایش به صورت نزولی"},
    "autoFill": {
        "cancel": "انصراف",
        "fill": "پر کردن همه سلول ها با ساختار سیستم",
        "fillHorizontal": "پر کردن سلول به صورت افقی",
        "fillVertical": "پرکردن سلول به صورت عمودی"
    },
    "buttons": {
        "collection": "مجموعه",
        "colvis": "قابلیت نمایش ستون",
        "colvisRestore": "بازنشانی قابلیت نمایش",
        "copy": "کپی",
        "copySuccess": {"1": "یک ردیف داخل حافظه کپی شد", "_": "%ds ردیف داخل حافظه کپی شد"},
        "copyTitle": "کپی در حافظه",
        "pageLength": {"-1": "نمایش همه ردیف‌ها", "_": "نمایش %d ردیف", "1": "نمایش 1 ردیف"},
        "print": "چاپ",
        "csv": "فایل CSV",
        "pdf": "فایل PDF",
        "excel": "فایل اکسل"
    },
    "loadingRecords": "در حال بارگذاری...",
    "decimal": ".",
    "thousands": ","
};

// ─── توابعی که از onclick در HTML صدا زده می‌شن باید global باشن ──────────────

window.jvToggle = function(btn) {
    // ساختار: .jv-node > [toggle] [key/index] [colon] [bracket + children + bracket]
    // children مستقیم زیر همون .jv-node
    const node     = btn.closest('.jv-node');
    const children = Array.from(node.childNodes).find(
        n => n.nodeType === 1 && n.classList.contains('jv-children')
    ) || node.querySelector(':scope > .jv-children');

    if (!children) return;

    if (btn.classList.toggle('jv-open')) {
        children.style.display = '';
    } else {
        children.style.display = 'none';
    }
};


function jvCopy(btn, encodedJson) {
    const jsonString = decodeURIComponent(encodedJson);

    function flash() {
        const original = btn.innerHTML;
        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"/></svg> کپی شد!`;
        btn.classList.add('jv-copy-btn--success');
        setTimeout(() => {
            btn.innerHTML = original;
            btn.classList.remove('jv-copy-btn--success');
        }, 2000);
    }

    if (navigator.clipboard) {
        navigator.clipboard.writeText(jsonString).then(flash).catch(() => fallbackCopy(jsonString, flash));
    } else {
        fallbackCopy(jsonString, flash);
    }
}

function fallbackCopy(text, callback) {
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
    if (callback) callback();
}

// ─── main ──────────────────────────────────────────────────────────────────────
$(document).ready(function () {

    // ─── Config ───────────────────────────────────────────────────────────────
    const cfg = window.dblogger || {};
    const API_URL      = cfg.apiUrl      || '/dblogger/api/logs';
    const SHOW_BASE_URL = cfg.showUrl    || '/dblogger/logs/get';
    const MSG_LIMIT    = cfg.msgLimit    || 80;
    const RT_MIN       = cfg.responseTimeMin || 1;
    const RT_MAX       = cfg.responseTimeMax || 3;
    const AJAX_TIMEOUT = 120000;

    let table = null;
    let loadingTimer = null;
    let loadingStartTime = null;

    // ذخیره داده هر تب برای کپی
    const tabData = {};

    // ─── Helpers ──────────────────────────────────────────────────────────────
    function levelBadge(level) {
        const map = {'ERROR': 'bg-danger', 'SUCCESS': 'bg-success'};
        const cls = map[level] || 'bg-info';
        return `<small class="badge ${cls}">${escapeHtml(level)}</small>`;
    }

    function typeBadge(type) {
        const map = {'PROCESS': 'bg-warning', 'SERVICE': 'bg-secondary'};
        const cls = map[type] || 'bg-primary';
        return `<small class="badge ${cls}">${escapeHtml(type)}</small>`;
    }

    function rtBadge(rt) {
        let cls = 'bg-success';
        if (rt > RT_MIN && rt < RT_MAX) cls = 'bg-warning';
        if (rt >= RT_MAX) cls = 'bg-danger';
        return `<h5>s <span class="badge ${cls}">${escapeHtml(String(rt))}</span></h5>`;
    }

    function strLimit(str, limit) {
        if (!str) return '';
        return str.length > limit ? str.substring(0, limit) + '...' : str;
    }

    function escapeHtml(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ─── Loading timer ────────────────────────────────────────────────────────
    function startLoadingTimer() {
        loadingStartTime = Date.now();
        clearInterval(loadingTimer);
        loadingTimer = setInterval(function () {
            const elapsed = Math.floor((Date.now() - loadingStartTime) / 1000);
            $('#loading-elapsed').text(elapsed + ' ثانیه گذشت...');
        }, 1000);
    }

    function stopLoadingTimer() {
        clearInterval(loadingTimer);
        loadingTimer = null;
        $('#loading-elapsed').text('');
    }

    function showTableLoading() {
        $('#table-loading-overlay').addClass('active');
        startLoadingTimer();
    }

    function hideTableLoading() {
        $('#table-loading-overlay').removeClass('active');
        stopLoadingTimer();
    }

    function showError(msg) {
        $('#api-error-msg').text(msg);
        $('#api-error-banner').show();
    }

    function hideError() {
        $('#api-error-banner').hide();
    }

    function getFilters() {
        return {
            search: $('#input-search').val() || '',
            date:   $('#input-date').val()   || '',
            type:   $('#input-type').val()   || '',
            level:  $('#input-level').val()  || '',
        };
    }

    // ─── JSON helpers ─────────────────────────────────────────────────────────
    function deepParseJson(value) {
        if (typeof value === 'string') {
            try {
                return deepParseJson(JSON.parse(value));
            } catch (e) {
                return value;
            }
        }
        if (Array.isArray(value)) {
            return value.map(item => deepParseJson(item));
        }
        if (value !== null && typeof value === 'object') {
            const result = {};
            for (const key of Object.keys(value)) {
                result[key] = deepParseJson(value[key]);
            }
            return result;
        }
        return value;
    }

    function buildJsonViewer(data, depth) {
        depth = depth || 0;

        if (data === null)
            return '<span class="jv-null">null</span>';
        if (data === undefined)
            return '<span class="jv-null">undefined</span>';
        if (typeof data === 'boolean')
            return `<span class="jv-bool">${data}</span>`;
        if (typeof data === 'number')
            return `<span class="jv-num">${data}</span>`;
        if (typeof data === 'string')
            return `<span class="jv-str">"${escapeHtml(data)}"</span>`;

        if (Array.isArray(data)) {
            if (data.length === 0)
                return '<span class="jv-bracket">[</span><span class="jv-bracket">]</span>';

            const items = data.map((item, i) => {
                const isLast    = i === data.length - 1;
                const isComplex = item !== null && typeof item === 'object';
                const comma     = isLast ? '' : '<span class="jv-comma">,</span>';

                if (isComplex) {
                    return `
                <div class="jv-node">
                    <span class="jv-toggle jv-open" onclick="jvToggle(this)"></span><span class="jv-index">${i}</span><span class="jv-colon">: </span>${buildJsonViewer(item, depth + 1)}${comma}
                </div>`;
                }
                return `
                <div class="jv-node jv-leaf">
                    <span class="jv-toggle-placeholder"></span><span class="jv-index">${i}</span><span class="jv-colon">: </span>${buildJsonViewer(item, depth + 1)}${comma}
                </div>`;
            }).join('');

            return `<span class="jv-bracket">[</span><div class="jv-children">${items}</div><span class="jv-bracket">]</span>`;
        }

        if (typeof data === 'object') {
            const keys = Object.keys(data);
            if (keys.length === 0)
                return '<span class="jv-bracket">{</span><span class="jv-bracket">}</span>';

            const items = keys.map((key, i) => {
                const val       = data[key];
                const isLast    = i === keys.length - 1;
                const isComplex = val !== null && typeof val === 'object';
                const comma     = isLast ? '' : '<span class="jv-comma">,</span>';

                if (isComplex) {
                    return `
                <div class="jv-node">
                    <span class="jv-toggle jv-open" onclick="jvToggle(this)"></span><span class="jv-key">"${escapeHtml(key)}"</span><span class="jv-colon">: </span>${buildJsonViewer(val, depth + 1)}${comma}
                </div>`;
                }
                return `
                <div class="jv-node jv-leaf">
                    <span class="jv-toggle-placeholder"></span><span class="jv-key">"${escapeHtml(key)}"</span><span class="jv-colon">: </span>${buildJsonViewer(val, depth + 1)}${comma}
                </div>`;
            }).join('');

            return `<span class="jv-bracket">{</span><div class="jv-children">${items}</div><span class="jv-bracket">}</span>`;
        }

        return escapeHtml(String(data));
    }

    // tabKey اختیاریه — اگه داده بشه، در tabData ذخیره می‌شه
    function renderJsonViewer(selector, data, tabKey) {
        const parsed = (data === null || data === undefined) ? null : deepParseJson(data);
        const container = $(selector);

        // ذخیره برای کپی بیرونی (در صورت نیاز)
        if (tabKey) tabData[tabKey] = parsed;

        if (parsed === null) {
            container.html('<div class="jv-toolbar"></div><span class="jv-null p-2 d-block">داده‌ای وجود ندارد</span>');
            return;
        }

        const jsonString  = JSON.stringify(parsed, null, 2);
        const encodedJson = encodeURIComponent(jsonString);

        container.html(`
            <div class="jv-toolbar">
                <button class="jv-copy-btn" onclick="jvCopy(this, '${encodedJson}')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2"/>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                    </svg>
                    کپی JSON
                </button>
            </div>
            <div class="json-viewer">${buildJsonViewer(parsed)}</div>
        `);
    }

    // ─── DataTable ────────────────────────────────────────────────────────────
    function initDatatable() {
        table = $('#inbox-table').DataTable({
            language: faLang,
            pageLength: 10,
            processing: false,
            serverSide: true,
            deferRender: true,
            columnDefs: [
                {targets: [0, 1, 2, 3, 4, 5, 6], orderable: false},
            ],
            ajax: {
                url: API_URL,
                type: 'GET',
                timeout: AJAX_TIMEOUT,
                data: function (d) {
                    const f = getFilters();
                    d.page     = Math.floor(d.start / d.length) + 1;
                    d.per_page = d.length;
                    d.search   = f.search;
                    d.date     = f.date;
                    d.type     = f.type;
                    d.level    = f.level;
                },
                beforeSend: function () {
                    hideError();
                    showTableLoading();
                },
                dataSrc: function (json) {
                    hideTableLoading();
                    if (json && json.meta) {
                        json.recordsTotal    = json.meta.total;
                        json.recordsFiltered = json.meta.total;
                    }
                    return json.data || [];
                },
                error: function (xhr, error, thrown) {
                    hideTableLoading();
                    let msg = 'خطای ناشناخته دریافت داده‌ها';
                    if (error === 'timeout')                         msg = 'زمان انتظار به پایان رسید.';
                    else if (xhr.status === 0)                       msg = 'اتصال به سرور برقرار نشد.';
                    else if (xhr.status === 401 || xhr.status === 403) msg = 'دسترسی مجاز نیست (' + xhr.status + ').';
                    else if (xhr.status === 500)                     msg = 'خطای داخلی سرور (500).';
                    else if (thrown)                                 msg = thrown;
                    showError(msg);
                },
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.settings._iDisplayStart + meta.row + 1;
                    }
                },
                {
                    data: 'created_at',
                    render: function (data) {
                        if (!data) return '-';
                        try { return jdate(data).format('HH:mm:ss - Y/m/d'); }
                        catch (e) { return data; }
                    }
                },
                {
                    data: 'level',
                    render: function (data) { return levelBadge(data || ''); }
                },
                {
                    data: 'type',
                    render: function (data) { return typeBadge(data || ''); }
                },
                {
                    data: 'message',
                    render: function (data) {
                        return `<small>${escapeHtml(strLimit(data, MSG_LIMIT))}</small>`;
                    }
                },
                {
                    data: 'response_time',
                    render: function (data) { return rtBadge(data); }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<button type="button"
                            class="btn btn-info btn-sm show-input"
                            data-bs-toggle="modal"
                            data-bs-target="#exampleModal"
                            data-id="${row.id}">
                            نمایش
                        </button>`;
                    }
                },
            ],
        });
    }

    initDatatable();

    // ─── Modal: نمایش جزئیات ──────────────────────────────────────────────────
    $(document).on('click', '.show-input', function () {
        const logId = $(this).data('id');
        const url   = SHOW_BASE_URL.replace('{id}', logId);

        $('#modal-loading').show();
        $('#modal-content-wrapper').hide();

        // برگشت به تب اول
        $('#home-tab').tab('show');

        $.ajax({
            url: url,
            type: 'GET',
            timeout: AJAX_TIMEOUT,
            success: function (response) {
                const data = response.data ?? response;

                renderJsonViewer('#json_detail', {
                    id:            data.id,
                    level:         data.level,
                    type:          data.type,
                    service:       data.service,
                    uri:           data.uri,
                    method:        data.method,
                    status_code:   data.status_code,
                    response_time: data.response_time,
                    user:          data.user,
                    message:       data.message,
                    created_at:    data.created_at,
                });

                renderJsonViewer('#json_input',     data.input);
                renderJsonViewer('#json_output',    data.output);
                renderJsonViewer('#json_context',   data.context);
                renderJsonViewer('#json_extradata', data.extra_data);

                $('#modal-loading').hide();
                $('#modal-content-wrapper').show();
            },
            error: function (xhr) {
                $('#modal-loading').hide();
                $('#modal-content-wrapper').html(
                    '<div class="alert alert-danger m-3">خطا در دریافت اطلاعات: ' +
                    escapeHtml(xhr.responseJSON?.message ?? 'خطای ناشناخته') +
                    '</div>'
                );
                $('#modal-content-wrapper').show();
            }
        });
    });

    // ─── فیلترها: reload جدول ─────────────────────────────────────────────────
    let searchDebounce = null;
    $('#input-search').on('input', function () {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(function () {
            if (table) table.ajax.reload(null, true);
        }, 500); // 500ms صبر می‌کنه بعد از آخرین کاراکتر
    });

    $('#input-date, #input-type, #input-level').on('change', function () {
        if (table) table.ajax.reload(null, true);
    });

    // ─── دکمه فیلتر ───────────────────────────────────────────────────────────────
    $('#submit-btn').on('click', function () {
        if (table) table.ajax.reload(null, true);
    });

// ─── دکمه ریست ────────────────────────────────────────────────────────────────
    $('#reset-btn').on('click', function () {
        $('#input-search').val('');
        $('#input-level').val('');
        $('#input-type').val('');

        // ریست flatpickr
        const fp = document.querySelector('#input-date')?._flatpickr;
        if (fp) {
            fp.clear();
        } else {
            $('#input-date').val('');
        }

        if (table) table.ajax.reload(null, true);
    });

});

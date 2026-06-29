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
$(document).ready(function () {

    // ─── Config ───────────────────────────────────────────────────────────────
    const cfg = window.dblogger || {};
    const API_URL = cfg.apiUrl || '/dblogger/api/logs';
    const SHOW_BASE_URL = cfg.showUrl || '/dblogger/logs/get';
    const MSG_LIMIT = cfg.msgLimit || 80;
    const RT_MIN = cfg.responseTimeMin || 1;
    const RT_MAX = cfg.responseTimeMax || 3;

    // timeout طولانی چون سرویس کنده (120 ثانیه)
    const AJAX_TIMEOUT = 120000;

    let table = null;
    let loadingTimer = null;
    let loadingStartTime = null;

    // ─── Helper: badge level ──────────────────────────────────────────────────
    function levelBadge(level) {
        const map = {'ERROR': 'bg-danger', 'SUCCESS': 'bg-success'};
        const cls = map[level] || 'bg-info';
        return `<small class="badge ${cls}">${escapeHtml(level)}</small>`;
    }

    // ─── Helper: badge type ───────────────────────────────────────────────────
    function typeBadge(type) {
        const map = {'PROCESS': 'bg-warning', 'SERVICE': 'bg-secondary'};
        const cls = map[type] || 'bg-primary';
        return `<small class="badge ${cls}">${escapeHtml(type)}</small>`;
    }

    // ─── Helper: badge response time ──────────────────────────────────────────
    function rtBadge(rt) {
        let cls = 'bg-success';
        if (rt > RT_MIN && rt < RT_MAX) cls = 'bg-warning';
        if (rt >= RT_MAX) cls = 'bg-danger';
        return `<h5>s <span class="badge ${cls}">${escapeHtml(String(rt))}</span></h5>`;
    }

    // ─── Helper: truncate string ──────────────────────────────────────────────
    function strLimit(str, limit) {
        if (!str) return '';
        return str.length > limit ? str.substring(0, limit) + '...' : str;
    }

    // ─── Helper: escape HTML ──────────────────────────────────────────────────
    function escapeHtml(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ─── Helper: نمایش elapsed time در loading ────────────────────────────────
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

    // ─── نمایش/پنهان کردن overlay ─────────────────────────────────────────────
    function showTableLoading() {
        $('#table-loading-overlay').addClass('active');
        startLoadingTimer();
    }

    function hideTableLoading() {
        $('#table-loading-overlay').removeClass('active');
        stopLoadingTimer();
    }

    // ─── نمایش خطا ────────────────────────────────────────────────────────────
    function showError(msg) {
        $('#api-error-msg').text(msg);
        $('#api-error-banner').show();
    }

    function hideError() {
        $('#api-error-banner').hide();
    }

    // ─── گرفتن فیلترهای فعلی ─────────────────────────────────────────────────
    function getFilters() {
        return {
            search: $('#input-search').val() || '',
            date: $('#input-date').val() || '',
            type: $('#input-type').val() || '',
            level: $('#input-level').val() || '',
        };
    }

    // ─── init DataTable با ajax ───────────────────────────────────────────────
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
                    d.page = Math.floor(d.start / d.length) + 1;
                    d.per_page = d.length;
                    d.search = f.search;
                    d.date = f.date;
                    d.type = f.type;
                    d.level = f.level;
                },
                beforeSend: function () {
                    hideError();
                    showTableLoading();
                },
                dataSrc: function (json) {
                    hideTableLoading();
                    if (json && json.meta) {
                        json.recordsTotal = json.meta.total;
                        json.recordsFiltered = json.meta.total;
                    }
                    return json.data || [];
                },
                error: function (xhr, error, thrown) {
                    hideTableLoading();
                    let msg = 'خطای ناشناخته دریافت داده‌ها';
                    if (error === 'timeout') msg = 'زمان انتظار به پایان رسید.';
                    else if (xhr.status === 0) msg = 'اتصال به سرور برقرار نشد.';
                    else if (xhr.status === 401 || xhr.status === 403) msg = 'دسترسی مجاز نیست (' + xhr.status + ').';
                    else if (xhr.status === 500) msg = 'خطای داخلی سرور (500).';
                    else if (thrown) msg = thrown;
                    showError(msg);
                },
            },
            // ← columns اینجا، همتراز با ajax
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
                        try {
                            return jdate(data).format('HH:mm:ss - Y/m/d');
                        } catch (e) {
                            return data;
                        }
                    }
                },
                {
                    data: 'level',
                    render: function (data) {
                        return levelBadge(data || '');
                    }
                },
                {
                    data: 'type',
                    render: function (data) {
                        return typeBadge(data || '');
                    }
                },
                {
                    data: 'message',
                    render: function (data) {
                        return `<small>${escapeHtml(strLimit(data, MSG_LIMIT))}</small>`;
                    }
                },
                {
                    data: 'response_time',
                    render: function (data) {
                        return rtBadge(data);
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        const detailUrl = SHOW_BASE_URL + '/' + row.id + '/input';
                        return `<button type="button"
                            class="btn btn-info btn-sm show-input"
                            data-bs-toggle="modal"
                            data-bs-target="#exampleModal"
                            data-id="${row.id}"
                            data-url="${detailUrl}">
                            نمایش
                        </button>`;
                    }
                },
            ],
        });  // ← بستن DataTable({})
    }        // ← بستن initDatatable()


    initDatatable();

    $(document).on('click', '.show-input', function () {
        const logId = $(this).data('id');
        const url = window.dblogger.showUrl.replace('{id}', logId);

        // نمایش loading، مخفی کردن content
        $('#modal-loading').show();
        $('#modal-content-wrapper').hide();
        $('#exampleModal').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                const data = response.data ?? response;

                $('#json_detail').text(formatJson({
                    id: data.id,
                    level: data.level,
                    type: data.type,
                    service: data.service,
                    uri: data.uri,
                    method: data.method,
                    status_code: data.status_code,
                    response_time: data.response_time,
                    user: data.user,
                    message: data.message,
                    created_at: data.created_at,
                }));

                $('#json_input').text(formatJson(data.input));
                $('#json_output').text(formatJson(data.output));
                $('#json_context').text(formatJson(data.context));
                $('#json_extradata').text(formatJson(data.extra_data));

                $('#modal-loading').hide();
                $('#modal-content-wrapper').show();
            },
            error: function (xhr) {
                $('#modal-loading').hide();
                $('#modal-content-wrapper').html(
                    '<div class="alert alert-danger">خطا در دریافت اطلاعات: ' + (xhr.responseJSON?.message ?? 'خطای ناشناخته') + '</div>'
                );
                $('#modal-content-wrapper').show();
            }
        });
    });

    function formatJson(value) {
        if (value === null || value === undefined) {
            return 'داده‌ای وجود ندارد';
        }
        if (typeof value === 'string') {
            // اگه string بود، سعی کن parse کنه
            try {
                return JSON.stringify(JSON.parse(value), null, 2);
            } catch (e) {
                return value;
            }
        }
        return JSON.stringify(value, null, 2);
    }

});
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
            serverSide: true,          // ← تغییر
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
                    // DataTables خودش start و length می‌فرسته
                    // ما اونا رو به page و per_page تبدیل می‌کنیم
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
                    // پاسخ Laravel Resource Collection:
                    // { data: [...], meta: { total, current_page, ... } }
                    if (json && json.meta) {
                        // به DataTables بگو کل رکوردها چنده
                        json.recordsTotal = json.meta.total;
                        json.recordsFiltered = json.meta.total;
                    }
                    return json.data || [];
                },
                error: function (xhr, error, thrown) {
                    hideTableLoading();
                    let msg = 'خطایناشناخته دریافت داده‌ها';
                    if (error === 'timeout') msg = 'زمان انتظار به پایان رسید.';
                    else if (xhr.status === 0) msg = 'اتصال به سرور برقرار نشد.';
                    else if (xhr.status === 401 || xhr.status === 403) msg = 'دسترسی مجاز نیست (' + xhr.status + ').';
                    else if (xhr.status === 500) msg = 'خطای داخلی سرور (500).';
                    else if (thrown) msg = thrown;
                    showError(msg);
                },
                // ─── تبدیل داده به سطر جدول ──────────────────────────────────────
                columns: [
                    {
                        // شماره ردیف — از row index استفاده می‌کنیم
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
                                // jdate باید در scope باشه (از libs لود شده)
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
            });
    }

        // ─── reload جدول با فیلترهای جدید ────────────────────────────────────────
        function reloadTable() {
            if (table) {
                table.ajax.reload(null, false); // false = صفحه فعلی حفظ بشه
            }
        }

        // ─── فیلتر: submit ────────────────────────────────────────────────────────
        $(document).on('click', '#submit-btn', function (e) {
            e.preventDefault();
            reloadTable();
        });

        // ─── فیلتر: reset ─────────────────────────────────────────────────────────
        $(document).on('click', '#reset-btn', function (e) {
            e.preventDefault();
            $('#input-search').val('');
            $('#input-date').val('');
            $('#input-type').prop('selectedIndex', 0);
            $('#input-level').prop('selectedIndex', 0);
            reloadTable();
        });

        // ─── JSON Helpers ─────────────────────────────────────────────────────────
        function IsValidJson(text) {
            if (typeof text !== 'string') return false;
            try {
                JSON.parse(text);
                return true;
            } catch (e) {
                return false;
            }
        }

        function prepareData(input) {
            try {
                input = (input !== null && input !== '') ? JSON.parse(input) : input;
                for (const key in input) {
                    for (const innerKey in input[key]) {
                        let index = 0;
                        if (typeof input[key][innerKey] === 'object') {
                            if (Array.isArray(input[key][innerKey])) {
                                for (const innerKey2 of input[key][innerKey]) {
                                    for (const itemKey in innerKey2) {
                                        input[key][innerKey][index][itemKey] = IsValidJson(input[key][innerKey][index][itemKey])
                                            ? JSON.parse(input[key][innerKey][index][itemKey])
                                            : input[key][innerKey][index][itemKey];
                                    }
                                    index++;
                                }
                            } else {
                                for (const innerKeyKey in input[key][innerKey]) {
                                    input[key][innerKey][innerKeyKey] = IsValidJson(input[key][innerKey][innerKeyKey])
                                        ? JSON.parse(input[key][innerKey][innerKeyKey])
                                        : input[key][innerKey][innerKeyKey];
                                }
                            }
                        }
                        input[key][innerKey] = IsValidJson(input[key][innerKey])
                            ? JSON.parse(input[key][innerKey])
                            : input[key][innerKey];
                    }
                }
            } catch (e) {
            }
            return input;
        }

        // ─── Modal: نمایش جزئیات لاگ ─────────────────────────────────────────────
        // event delegation چون دکمه‌ها داینامیک رندر می‌شن
        $(document).on('click', '.show-input', function () {
            const url = $(this).data('url');

            // reset تب‌ها به حالت اول
            $('#home-tab').tab('show');

            // نمایش loading داخل مودال
            $('#modal-loading').show();
            $('#modal-content-wrapper').hide();
            $('#json_input, #json_output, #json_context, #json_detail, #json_extradata').html('');

            $.ajax({
                type: 'GET',
                url: url,
                timeout: AJAX_TIMEOUT,
                success: function (response) {
                    $('#modal-loading').hide();
                    $('#modal-content-wrapper').show();

                    let data = response.data;

                    data.output = prepareData(data.output);
                    data.input = prepareData(data.input);
                    data.context = prepareData(data.context);
                    data.extra_data = prepareData(data.extra_data);

                    if (data.output === null) data.output = {};
                    data.output.message = prepareData(data.message);

                    // input tab
                    if (data.input === null) {
                        $('#input-tab').hide();
                    } else {
                        $('#input-tab').show();
                        $('#json_input').html(prettyPrintJson.toHtml(data.input));
                    }

                    // output tab
                    if (data.output === null) {
                        $('#contact-tab').hide();
                    } else {
                        $('#contact-tab').show();
                        $('#json_output').html(prettyPrintJson.toHtml(data.output));
                    }

                    // context tab
                    if (data.context === null) {
                        $('#context-tab').hide();
                    } else {
                        $('#context-tab').show();
                        $('#json_context').html(prettyPrintJson.toHtml(data.context));
                    }

                    // extra_data tab
                    if (data.extra_data === null) {
                        $('#extradata-tab').hide();
                    } else {
                        $('#extradata-tab').show();
                        $('#json_extradata').html(prettyPrintJson.toHtml(data.extra_data));
                    }

                    // detail tab — بقیه فیلدها بعد از حذف relation‌ها
                    delete data.input;
                    delete data.output;
                    delete data.context;
                    delete data.extra_data;
                    delete data.message;
                    $('#json_detail').html(prettyPrintJson.toHtml(data));
                },
                error: function (xhr, error) {
                    $('#modal-loading').hide();
                    $('#modal-content-wrapper').show();

                    let msg = 'خطا در دریافت جزئیات لاگ.';
                    if (error === 'timeout') msg = 'زمان انتظار به پایان رسید.';

                    $('#json_detail').html(`<div class="alert alert-danger" style="direction:rtl">${msg}</div>`);
                }
            });
        });


        initDatatable();

    }

)
    ;
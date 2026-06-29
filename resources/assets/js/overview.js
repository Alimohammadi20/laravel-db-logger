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
    "paginate": { "next": "بعدی", "previous": "قبلی", "first": "ابتدا", "last": "انتها" },
    "aria": { "sortAscending": ": فعال سازی نمایش به صورت صعودی", "sortDescending": ": فعال سازی نمایش به صورت نزولی" },
    "autoFill": { "cancel": "انصراف", "fill": "پر کردن همه سلول ها با ساختار سیستم", "fillHorizontal": "پر کردن سلول به صورت افقی", "fillVertical": "پرکردن سلول به صورت عمودی" },
    "buttons": { "collection": "مجموعه", "colvis": "قابلیت نمایش ستون", "colvisRestore": "بازنشانی قابلیت نمایش", "copy": "کپی", "copySuccess": { "1": "یک ردیف داخل حافظه کپی شد", "_": "%ds ردیف داخل حافظه کپی شد" }, "copyTitle": "کپی در حافظه", "pageLength": { "-1": "نمایش همه ردیف‌ها", "_": "نمایش %d ردیف", "1": "نمایش 1 ردیف" }, "print": "چاپ", "csv": "فایل CSV", "pdf": "فایل PDF", "excel": "فایل اکسل" },
    "loadingRecords": "در حال بارگذاری...",
    "decimal": ".",
    "thousands": ","
};
$(document).ready(function () {

    loadOverview();

    function loadOverview() {
        $('#overview-loading').show();
        $('#overview-content').hide();
        $('#overview-error').hide();

        $.ajax({
            url: window.dblogger.overviewApiUrl,
            type: 'GET',
            success: function (response) {
                renderOverview(response.data);
                $('#overview-loading').hide();
                $('#overview-content').show();
            },
            error: function () {
                $('#overview-loading').hide();
                $('#overview-error').show();
            }
        });
    }

    function renderOverview(data) {
        const container = $('#overview-container');
        container.empty();

        const dates = Object.keys(data);

        if (dates.length === 0) {
            container.html('<div class="alert alert-info">لاگی وجود ندارد</div>');
            return;
        }

        dates.forEach(function (jalaliDate) {
            const levels = data[jalaliDate];

            // badge های level
            let badgesHtml = '';
            levels.forEach(function (item) {
                const badgeClass = getBadgeClass(item.level);
                badgesHtml += `
                    <span class="badge bg-${badgeClass} me-1">
                        ${item.level}: ${item.count.toLocaleString('fa-IR')}
                    </span>`;
            });

            // محاسبه مجموع
            const total = levels.reduce((sum, item) => sum + item.count, 0);

            // دکمه حذف
            const deleteUrl = `${window.dblogger.deleteBaseUrl}/${jalaliDate}/destroy`;

            const card = `
                <div class="col-md-3">
                <div class="card mb-2 overview-card" id="card-${jalaliDate.replace(/\//g, '-')}">
                    <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <strong class="text-muted">${jalaliDate}</strong>
                            <div>${badgesHtml}</div>
                        </div>
                        <hr>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark">مجموع: ${total.toLocaleString('fa-IR')}</span>
                            <button class="btn btn-sm btn-outline-danger delete-logs"
                                data-url="${deleteUrl}"
                                data-target="card-${jalaliDate.replace(/\//g, '-')}">
                                <i class="fas fa-trash"></i>
                            </button>
                            <a href="${window.dblogger.showUrl + '?date=' + jalaliDate.replace(/\//g, '-')}" class="btn btn-sm btn-outline-success"
                                data-target="card-${jalaliDate.replace(/\//g, '-')}">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>`;

            container.append(card);
        });

        // bind delete
        bindDelete();
    }

    function bindDelete() {
        $(document).off('click', '.delete-logs').on('click', '.delete-logs', function () {
            const url = $(this).data('url');
            const target = $(this).data('target');

            Swal.fire({
                title: 'حذف لاگ‌ها',
                text: 'لاگ‌های این روز حذف شوند؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'بله، حذف شود',
                cancelButtonText: 'انصراف',
                confirmButtonColor: '#d33',
            }).then(function (result) {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (res) {
                        if (res.status) {
                            $('#' + target).fadeOut(300, function () {
                                $(this).remove();
                            });
                        } else {
                            Swal.fire('خطا', 'حذف انجام نشد', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('خطا', 'مشکل در ارتباط با سرور', 'error');
                    }
                });
            });
        });
    }

    function getBadgeClass(level) {
        const map = {
            'ERROR': 'danger',
            'SUCCESS': 'success',
            'WARNING': 'warning',
            'INFO': 'info',
        };
        return map[level] || 'secondary';
    }

});
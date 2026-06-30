{{-- filters.blade.php --}}
<form class="row g-3 align-items-end" method="get" id="filter-form">

    <div class="col-md-3">
        <label class="form-label fw-semibold small text-secondary" for="input-search">
            جستجو در پیام / URI / کاربر
        </label>
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.099zm-5.242 1.156a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11"/>
                </svg>
            </span>
            <input type="text" name="search" id="input-search"
                   class="form-control border-start-0"
                   value="{{ request('search') }}"
                   placeholder="جستجو...">
        </div>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold small text-secondary" for="input-date">
            بازه تاریخ
        </label>
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                </svg>
            </span>
            <input type="text" class="form-control flatpickr-range border-start-0"
                   autocomplete="off" value="{{ request('date') }}"
                   id="input-date" name="date" placeholder="انتخاب بازه تاریخ">
        </div>
    </div>

    <div class="col-md-2">
        <label class="form-label fw-semibold small text-secondary" for="input-level">
            سطح لاگ
        </label>
        <select name="level" class="form-select form-select-sm" id="input-level">
            <option value="" {{ !request('level') ? 'selected' : '' }}>همه سطوح</option>
            @foreach(\Alimi7372\DBLogger\Enums\LogLevel::cases() as $logLevel)
                <option value="{{ $logLevel->name }}"
                        {{ request('level') === $logLevel->name ? 'selected' : '' }}>
                    {{ $logLevel->value }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label fw-semibold small text-secondary" for="input-searchContext">
            جستجو در کانتکست
        </label>
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M5.854 4.854a.5.5 0 1 0-.708-.708l-3.5 3.5a.5.5 0 0 0 0 .708l3.5 3.5a.5.5 0 0 0 .708-.708L2.707 8zm4.292 0a.5.5 0 0 1 .708-.708l3.5 3.5a.5.5 0 0 1 0 .708l-3.5 3.5a.5.5 0 0 1-.708-.708L13.293 8z"/>
                </svg>
            </span>
            <input type="text" name="searchContext" id="input-searchContext"
                   class="form-control border-start-0"
                   value="{{ request('searchContext') }}"
                   placeholder="کلید / مقدار...">
        </div>
    </div>

    <div class="col-md-2 d-flex gap-2">
        <button type="button" id="submit-btn"
                class="btn btn-primary btn-sm flex-fill fw-semibold">
            فیلتر
        </button>
        <button type="button" id="reset-btn"
                class="btn btn-outline-secondary btn-sm flex-fill fw-semibold">
            ریست
        </button>
    </div>

</form>

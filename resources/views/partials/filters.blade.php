<form class="row form-inline" method="get">
    <div class="col-md-3">
        <label class="form-label" for="input-search">search :</label>
        <input type="text" name="search" id="input-search" class="form-control" value="{{request('search')}}">
    </div>
    <div class="col-md-3 form-inline form-group">
        <!-- Date Picker-->
        <label class="form-label" for="input-date">تاریخ</label>
        <input type="text" class="form-control flatpickr-range"
               autocomplete="off" value="{{request('date')}}"
               aria-describedby="Help" id="input-date"
               name="date" placeholder="تاریخ">
        <!-- /Date Picker -->
    </div>
    <div class="col-md-2">
        <label class="form-label" for="input-level">level</label>
        <select type="text" name="level" class="form-control" id="input-level">
            <option {{!request('level') ? 'selected' : ''}}></option>
            @foreach(\Alimi7372\DBLogger\Enums\LogLevel::cases() as $logLevel)
                <option value="{{$logLevel->name}}" {{request('level') == $logLevel->name? 'selected' : ''}}>
                    {{$logLevel->value}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label" for="input-type">services :</label>
        <select type="text" name="type" class="form-control" id="input-type">
            <option {{!request('type') ? 'selected' : ''}}></option>
            @foreach(\Alimi7372\DBLogger\Enums\LogType::cases() as $type)
                <option value="{{$type->name}}" {{request('type') == $type->name? 'selected' : ''}}>
                    {{$type->value}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-2 text-start d-flex align-items-end justify-content-evenly">
        <button type="button" id="reset-btn" class="btn btn-danger ms-2">ریست</button>
        <button type="submit" id="submit-btn" class="btn btn-success">فیلتر</button>
    </div>
    {{--            <div class="col-md-3">--}}
    {{--                <label for="">type :</label>--}}
    {{--                <select type="text" name="type" class="form-control">--}}
    {{--                    <option value="PROCESS">process</option>--}}
    {{--                    <option value="INSERVICE">in service</option>--}}
    {{--                    <option value="OUTSERVICE">out service</option>--}}
    {{--                </select>--}}
    {{--            </div>--}}

</form>

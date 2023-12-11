<?php

namespace Alimi7372\DBLogger\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\CalendarUtils;

class Log extends Model
{
    protected $table = 'logs';
    protected $guarded = ['id'];
    protected $casts = [
        'created_at' => 'datetime:Y/m/d HH:MM:SS',
    ];

    public function setUpdatedAt($value)
    {
    }

    public function scopeFilter($query, $request)
    {
        if ($request->date) {
            $dates = explode(' تا ', $request->date);
            $fromDate = CalendarUtils::createCarbonFromFormat('Y-m-d', $dates[0])->format('Y-m-d');
            if (count($dates) == 2) {
                $toDate = CalendarUtils::createCarbonFromFormat('Y-m-d', $dates[1])->format('Y-m-d');
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            } else {
                $query->whereDate('created_at', '=', [$fromDate]);
            }
        }
        if ($request->search) {
            $query->where(function (Builder $query) use ($request) {
                return $query->orWhere('message', 'like', '%' . $request->search . '%')
                    ->orWhere('user', 'like', '%' . $request->search . '%')
                    ->orWhere('uri', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->level) {
            $query->where('level', $request->level);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        return $query->orderBy('id', 'desc')->get();
    }

    public function context()
    {
        return $this->belongsTo(LogContext::class, 'context_id', 'id');
    }

    public function input()
    {
        return $this->belongsTo(LogContext::class, 'input_id', 'id');
    }

    public function output()
    {
        return $this->belongsTo(LogContext::class, 'output_id', 'id');
    }

    public function extraData()
    {
        return $this->belongsTo(LogContext::class, 'extra_data_id', 'id');
    }
}

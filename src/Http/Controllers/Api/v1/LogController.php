<?php

namespace Alimi7372\DBLogger\Http\Controllers\Api\v1;

use Alimi7372\DBLogger\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\CalendarUtils;;

class LogController extends Controller
{
    public function indexApi(Request $request)
    {
        $logs = Log::filter($request);
        return response()->json($logs);
    }

    public function overviewApi()
    {
        $rows = Log::select(
            DB::raw('DATE(created_at) as created_date'),
            'level',
            DB::raw('COUNT(*) as count')
        )
            ->groupBy(DB::raw('DATE(created_at)'), 'level')
            ->orderBy('created_date', 'desc')
            ->get();

        // گروه‌بندی PHP-side — بدون N+1
        $data = [];
        foreach ($rows as $row) {
            // تبدیل میلادی به شمسی
            $jalali = CalendarUtils::createCarbonFromFormat('Y-m-d', $row->created_date)
                ->format('Y/m/d'); // یا هر فرمت شمسی که استفاده میکنی

            $data[$jalali][] = [
                'level' => $row->level,
                'count' => (int) $row->count,
            ];
        }

        return response()->json(['data' => $data]);
    }
}

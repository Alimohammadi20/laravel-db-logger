<?php

namespace Alimi7372\DBLogger\Http\Controllers\Api\v1;

use Alimi7372\DBLogger\Http\Resources\IndexLogResource;
use Alimi7372\DBLogger\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\CalendarUtils;

;

class LogController extends Controller
{
    public function indexApi(Request $request)
    {
        $logs = Log::filter($request)->paginate($request->input('per_page', 10));
        return IndexLogResource::collection($logs);
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

        $data = [];
        foreach ($rows as $row) {
            $jalali = jdate($row->created_date)->format('Y-m-d');
            $data[$jalali][] = [
                'level' => $row->level,
                'count' => (int)$row->count,
            ];
        }

        return response()->json(['data' => $data]);
    }
}

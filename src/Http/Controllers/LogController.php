<?php

namespace Alimi7372\DBLogger\Http\Controllers;

use Alimi7372\DBLogger\Http\Resources\LogResource;
use Alimi7372\DBLogger\Models\Log;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\CalendarUtils;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logs = Log::filter($request);
        return view('dblogger::index', compact('logs'));
    }

    public function indexApi(Request $request)
    {
        $logs = Log::all();
        return response()->json($logs);
    }

    public function getInput($id)
    {
        $log = Log::where('id', $id)->with(['input', 'output', 'context', 'extraData'])->first();
        return new LogResource($log);
    }

    public function overview()
    {

        $days = Log::select(DB::raw('DATE(created_at) as created_date'))
            ->distinct()
            ->pluck('created_date');
        $datas = [];
        foreach ($days as $day) {
            $log = Log::whereDate('created_at', '=', $day)->groupBy('level')
                ->selectRaw('level, COUNT(*) as count')
                ->get()->toArray();
            $datas[$day] = $log;
        }
        return view('dblogger::overview', compact('datas'));
    }

    public function destroy($date)
    {
        try {
            $date = CalendarUtils::createCarbonFromFormat('Y-m-d', $date)->format('Y-m-d');
            Log::whereDate('created_at', '=', $date)->delete();
            return response()->json(['status' => true]);
        } catch (Exception $ex) {
            return response()->json(['status' => false]);
        }
    }
}

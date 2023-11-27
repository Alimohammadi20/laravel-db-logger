<?php

namespace Alimi7372\DBLogger\Http\Controllers;

use Alimi7372\DBLogger\Http\Resources\LogResource;
use Alimi7372\DBLogger\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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
}

<?php

namespace App\Http\Controllers;

use App\Models\MonitorLog;
use App\Models\LogIgnore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LogDisplayController extends Controller
{
    public function index()
    {
        $logs = MonitorLog::whereNotExists(function ($query) {
            $query->select('id')
                  ->from('log_ignore')
                  ->whereColumn('log_ignore.description', 'monitor_logs.description')
                  ->whereColumn('log_ignore.client_id', 'monitor_logs.client_id');
        })->orderBy('created_at', 'desc')->get();

        return view('logs.index', compact('logs'));
    }

    public function ignore(Request $request)
    {
        Log::info('Received /logs/ignore request', ['input' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'log_id' => 'required|exists:monitor_logs,id',
            'description' => 'required|string',
            'client_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed in /logs/ignore', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        try {
            $description = html_entity_decode($request->input('description'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            LogIgnore::create([
                'client_id' => $request->input('client_id'),
                'description' => $description,
            ]);
            Log::info('Inserted into log_ignore', ['client_id' => $request->input('client_id'), 'description' => $description]);

            $log = MonitorLog::find($request->input('log_id'));
            if ($log) {
                $log->delete();
                Log::info('Deleted log from monitor_logs table', ['log_id' => $request->input('log_id')]);
            } else {
                Log::warning('Log not found for deletion', ['log_id' => $request->input('log_id')]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Error marked as negligible and moved to log_ignore',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in /logs/ignore', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
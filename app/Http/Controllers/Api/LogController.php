<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonitorLog;
use App\Models\LogIgnore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Received /api/logs request', ['input' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'Errors' => 'required|array',
            'Errors.*' => 'string',
            'client_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed in /api/logs', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        $inserted = 0;
        $skipped = 0;

        foreach ($request->input('Errors') as $error) {
            // Check if error exists in log_ignore with matching client_id and description
            $exists = LogIgnore::where('client_id', $request->input('client_id'))
                               ->where('description', $error)
                               ->exists();

            if (!$exists) {
                MonitorLog::create([
                    'client_id' => $request->input('client_id'),
                    'description' => $error,
                ]);
                $inserted++;
            } else {
                $skipped++;
            }
        }

        Log::info('Processed /api/logs', [
            'inserted' => $inserted,
            'skipped' => $skipped,
            'client_id' => $request->input('client_id'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Logs processed: $inserted inserted, $skipped skipped",
        ], 201);
    }
}
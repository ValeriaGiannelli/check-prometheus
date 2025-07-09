<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class PageController extends Controller
{
    public function index(){
        // Prometheus server URL (replace with your Prometheus server address)
        $prometheusUrl = 'http://localhost:9090/api/v1/query';


        // Query per vedere lo stato del server
        $query = 'up';

        try {
            // Make HTTP request to Prometheus API
            $response = Http::get($prometheusUrl, ['query' => $query]);

            // Check if request was successful
            if ($response->successful()) {
                $data = $response->json()['data']['result'];
                //dd($data); // Debugging output, remove in production
            } else {
                $data = []; // Handle error case
                \Log::error('Prometheus API request failed: ' . $response->status());
            }
        } catch (\Exception $e) {
            $data = [];
            \Log::error('Prometheus API error: ' . $e->getMessage());
        }

        // Pass data to the view
        return view('home', ['metrics' => $data]);
    }


    public function services(){
                // Prometheus server URL (replace with your Prometheus server address)
        $prometheusUrl = 'http://localhost:9090/api/v1/query';

        // Query per prendere i servizi che mi interessano
        $query = 'windows_service_state{name=~"MerlinCleaner|Merlin0"} ';

        try {
            // Make HTTP request to Prometheus API
            $response = Http::get($prometheusUrl, ['query' => $query]);

            // Check if request was successful
            if ($response->successful()) {
                $data = $response->json()['data']['result'];
                //dd($data); // Debugging output
            } else {
                $data = []; // Handle error case
                \Log::error('Prometheus API request failed: ' . $response->status());
            }
        } catch (\Exception $e) {
            $data = [];
            \Log::error('Prometheus API error: ' . $e->getMessage());
        }

        return view('services', ['metrics' => $data]);
    }

    public function sqlServerMetrics(){
        $prometheusUrl = 'http://localhost:9090/api/v1/query';
        $connectionQuery = 'mssql_up';
        $memoryQuery = 'mssql_sql_memory_utilization_percentage';
        $diskQuery = 'mssql_disk_usage_percent';

        $metrics = ['connection' => [], 'memory' => [], 'disk' => []];

        try {
            $connectionResponse = Http::get($prometheusUrl, ['query' => $connectionQuery]);
            if ($connectionResponse->successful()) {
                $metrics['connection'] = $connectionResponse->json()['data']['result'];
            } else {
                \Log::error('Prometheus API request failed for connection: ' . $connectionResponse->status());
            }

            $memoryResponse = Http::get($prometheusUrl, ['query' => $memoryQuery]);
            if ($memoryResponse->successful()) {
                $metrics['memory'] = $memoryResponse->json()['data']['result'];
            } else {
                \Log::error('Prometheus API request failed for memory: ' . $memoryResponse->status());
            }

            $diskResponse = Http::get($prometheusUrl, ['query' => $diskQuery]);
            if ($diskResponse->successful()) {
                $metrics['disk'] = $diskResponse->json()['data']['result'];
                //dd($diskResponse); // DEBUG
            } else {
                \Log::error('Prometheus API request failed for disk: ' . $diskResponse->status());
            }
        } catch (\Exception $e) {
            \Log::error('Prometheus API error: ' . $e->getMessage());
        }

        return view('sql_metrics', ['metrics' => $metrics]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class PageController extends Controller
{
    public function index(){

        // Prometheus server URL (replace with your Prometheus server address)
        $prometheusUrl = 'http://localhost:9090/api/v1/query';

        // Example query: Fetch a specific metric (e.g., 'up' to check if targets are up)
        $query = 'up';

        try {
            // Make HTTP request to Prometheus API
            $response = Http::get($prometheusUrl, ['query' => $query]);

            // Check if request was successful
            if ($response->successful()) {
                $data = $response->json()['data']['result'];
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


    public function about(){
        return view('about');
    }

    public function contacts(){
        return view('contacts');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                Log::error('Prometheus API request failed: ' . $response->status());
            }
        } catch (\Exception $e) {
            $data = [];
            Log::error('Prometheus API error: ' . $e->getMessage());
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
                Log::error('Prometheus API request failed: ' . $response->status());
            }
        } catch (\Exception $e) {
            $data = [];
            Log::error('Prometheus API error: ' . $e->getMessage());
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
                Log::error('Prometheus API request failed for connection: ' . $connectionResponse->status());
            }

            $memoryResponse = Http::get($prometheusUrl, ['query' => $memoryQuery]);
            if ($memoryResponse->successful()) {
                $metrics['memory'] = $memoryResponse->json()['data']['result'];
            } else {
                Log::error('Prometheus API request failed for memory: ' . $memoryResponse->status());
            }

            $diskResponse = Http::get($prometheusUrl, ['query' => $diskQuery]);
            if ($diskResponse->successful()) {
                $metrics['disk'] = $diskResponse->json()['data']['result'];
                //dd($diskResponse); // DEBUG
            } else {
                Log::error('Prometheus API request failed for disk: ' . $diskResponse->status());
            }
        } catch (\Exception $e) {
            Log::error('Prometheus API error: ' . $e->getMessage());
        }

        return view('sql_metrics', ['metrics' => $metrics]);
    }

     public function customerMetrics()
    {
        $prometheusUrl = 'http://localhost:9090/api/v1/query';
        $queries = [
            'connection' => 'mssql_up',
            'memory' => 'mssql_sql_memory_utilization_percentage > 60',
            'disk' => 'mssql_disk_usage_percent > 60',
            'active_serv' => 'windows_service_state{name=~"MerlinCleaner|Merlin0", state="stopped"} == 1'
        ];

        $customers = [];
        try {
            // Raccogli metriche per ogni query
            $metrics = [];
            foreach ($queries as $key => $query) {
                $response = Http::get($prometheusUrl, ['query' => $query]);
                if ($response->successful()) {
                    $metrics[$key] = $response->json()['data']['result'];
                    //dd($metrics[$key]);
                } else {
                    Log::error("Prometheus API request failed for $key: " . $response->status());
                    $metrics[$key] = [];
                }
            }

            // Aggrega per cliente
            $customerAlerts = [];
            foreach ($metrics as $key => $results) {
                foreach ($results as $result) {
                    $customer = $result['metric']['customer'] ?? 'Unknown';
                    $instance = $result['metric']['instance'] ?? 'N/A';
                    $database = $result['metric']['database'] ?? null;
                    $value = $result['value'][1];

                    if (!isset($customerAlerts[$customer])) {
                        $customerAlerts[$customer] = [
                            'instances' => [],
                        ];
                    }

                    if (!isset($customerAlerts[$customer]['instances'][$instance])) {
                        $customerAlerts[$customer]['instances'][$instance] = [
                            'sql_alerts' => 0,
                            'service_alerts' => 0,
                            'sql' => [],
                            'services' => [],
                            'encoded_instance' => urlencode($instance),
                        ];
                    }

                    if ($key === 'connection' && $value == '0') {
                        $customerAlerts[$customer]['instances'][$instance]['sql_alerts']++;
                        $customerAlerts[$customer]['instances'][$instance]['sql']['connection'] = 'Spento';
                    } elseif ($key === 'connection') {
                        $customerAlerts[$customer]['instances'][$instance]['sql']['connection'] = 'In esecuzione';
                    }

                    if ($key === 'memory') {
                        $customerAlerts[$customer]['instances'][$instance]['sql_alerts']++;
                        $customerAlerts[$customer]['instances'][$instance]['sql']['memory'] = $value;
                    }

                    if ($key === 'disk') {
                        $customerAlerts[$customer]['instances'][$instance]['sql_alerts']++;
                        $customerAlerts[$customer]['instances'][$instance]['sql']['disk'][$database] = $value;
                    }

                    if ($key === 'active_serv') {
                        $customerAlerts[$customer]['instances'][$instance]['service_alerts']++;
                        $customerAlerts[$customer]['instances'][$instance]['services']['active_serv'] = $value;
                    }

                }
            }

            $customers = $customerAlerts;
        } catch (\Exception $e) {
            Log::error('Prometheus API error: ' . $e->getMessage());
        }

        return view('customer_metrics', compact('customers'));
    }

    public function customerDetail($customer, $instance, $type)
    {
        $prometheusUrl = 'http://localhost:9090/api/v1/query';
        $queries = [
            'sql' => [
                'connection' => 'mssql_up{instance="' . $instance . '"}',
                'memory' => 'mssql_sql_memory_utilization_percentage{instance="' . $instance . '"}',
                'disk' => 'mssql_disk_usage_percent{instance="' . $instance . '"}',
            ],
            'services' => [
                'service' => 'windows_service_state{name=~"MerlinCleaner|Merlin0"} ',
            ],
        ];

        $metrics = [];
        try {
            foreach ($queries[$type] as $key => $query) {
                $response = Http::get($prometheusUrl, ['query' => $query]);
                if ($response->successful()) {
                    $metrics[$key] = $response->json()['data']['result'];
                } else {
                    Log::error("Prometheus API request failed for $key: " . $response->status());
                }
            }
        } catch (\Exception $e) {
            Log::error('Prometheus API error: ' . $e->getMessage());
        }

        return view('customer_detail', compact('customer', 'instance', 'type', 'metrics'));
    }

    // funzione per prendere le versioni del softeware e dei plugin
    public function getVersion()
    {
        $prometheusUrl = 'http://localhost:9090/api/v1/query';
        $softwareVersionQuery = 'merlin_software_info';
        $pluginVersionQuery = 'merlin_plugin_info';

        $metrics = ['software' => [], 'plugin' => []];
        
        try {
            $softwareResponse = Http::get($prometheusUrl, ['query' => $softwareVersionQuery]);
            if ($softwareResponse->successful()) {
                $metrics['software'] = $softwareResponse->json()['data']['result'];
            } else {
                Log::error('Prometheus API request failed for software version: ' . $softwareResponse->status());
            }
            
            $pluginResponse = Http::get($prometheusUrl, ['query' => $pluginVersionQuery]);
            if ($pluginResponse->successful()) {
                $metrics['plugin'] = $pluginResponse->json()['data']['result'];
            } else {
                Log::error('Prometheus API request failed for plugin version: ' . $pluginResponse->status());
            }

            //dd($metrics); // DEBUG
        } catch (\Exception $e) {
            Log::error('Prometheus API error: ' . $e->getMessage());
        }

        return view('info_version', ['metrics' => $metrics]);
    }
}

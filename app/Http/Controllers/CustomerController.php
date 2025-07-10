<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Yaml\Yaml;

class CustomerController extends Controller
{
    protected $prometheusConfigPath = 'C:\\Users\\v.giannelli\\Downloads\\prometheus-3.4.1.windows-amd64\\prometheus-3.4.1.windows-amd64\\prometheus.yml';


    public function index()
    {
        try {
            $config = Yaml::parse(file_get_contents($this->prometheusConfigPath));
        } catch (\Exception $e) {
            Log::error('Errore nella lettura di prometheus.yml: ' . $e->getMessage());
            return view('customers.index', ['customers' => [], 'error' => 'Impossibile leggere il file di configurazione.']);
        }

        $customers = [];
        foreach ($config['scrape_configs'] as $scrapeConfig) {
            if (in_array($scrapeConfig['job_name'], ['mssql', 'windows_exporter'])) {
                foreach ($scrapeConfig['static_configs'] as $staticConfig) {
                    $ip = str_replace([':4000', ':9182'], '', $staticConfig['targets'][0]);
                    $customer = $staticConfig['labels']['customer'] ?? 'Unknown';
                    $instance = $staticConfig['labels']['instance'] ?? $ip;
                    $customers[$customer][$instance][$scrapeConfig['job_name']] = $ip;
                }
            }
        }

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer' => 'required|string|max:255',
            'ip' => 'required|ip',
        ]);

        try {
            $config = Yaml::parseFile($this->prometheusConfigPath);
        } catch (\Exception $e) {
            Log::error('Errore nella lettura di prometheus.yml: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'Impossibile leggere il file di configurazione.');
        }

        $newMssqlTarget = [
            'targets' => ["{$request->ip}:4000"],
            'labels' => [
                'customer' => $request->customer,
                'instance' => $request->ip,
            ],
        ];

        $newWindowsTarget = [
            'targets' => ["{$request->ip}:9182"],
            'labels' => [
                'customer' => $request->customer,
                'instance' => $request->ip,
            ],
        ];

        foreach ($config['scrape_configs'] as &$scrapeConfig) {
            if ($scrapeConfig['job_name'] === 'mssql') {
                $scrapeConfig['static_configs'][] = $newMssqlTarget;
            }
            if ($scrapeConfig['job_name'] === 'windows_exporter') {
                $scrapeConfig['static_configs'][] = $newWindowsTarget;
            }
        }

        try {
            file_put_contents($this->prometheusConfigPath, Yaml::dump($config, 4, 2));
        } catch (\Exception $e) {
            Log::error('Errore nella scrittura di prometheus.yml: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'Impossibile scrivere il file di configurazione.');
        }

        // Ricarica Prometheus
        try {
            Http::post('http://localhost:9090/-/reload');
        } catch (\Exception $e) {
            Log::error('Failed to reload Prometheus: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'Cliente aggiunto, ma errore nel ricaricare Prometheus.');
        }

        return redirect()->route('customers.index')->with('success', 'Cliente aggiunto con successo.');
    }

    public function edit($customer, $ip)
    {
        return view('customers.edit', compact('customer', 'ip'));
    }

    public function update(Request $request, $customer, $ip)
    {
        $request->validate([
            'customer' => 'required|string|max:255',
            'ip' => 'required|ip',
        ]);

        try {
            $config = Yaml::parseFile($this->prometheusConfigPath);
        } catch (\Exception $e) {
            Log::error('Errore nella lettura di prometheus.yml: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'Impossibile leggere il file di configurazione.');
        }

        foreach ($config['scrape_configs'] as &$scrapeConfig) {
            if (in_array($scrapeConfig['job_name'], ['mssql', 'windows_exporter'])) {
                foreach ($scrapeConfig['static_configs'] as &$staticConfig) {
                    $currentIp = str_replace([':4000', ':9182'], '', $staticConfig['targets'][0]);
                    if ($currentIp === $ip && $staticConfig['labels']['customer'] === $customer) {
                        $staticConfig['labels']['customer'] = $request->customer;
                        if ($scrapeConfig['job_name'] === 'mssql') {
                            $staticConfig['targets'] = ["{$request->ip}:4000"];
                            $staticConfig['labels']['instance'] = $request->ip;
                        } else {
                            $staticConfig['targets'] = ["{$request->ip}:9182"];
                            $staticConfig['labels']['instance'] = $request->ip;
                        }
                    }
                }
            }
        }

        try {
            file_put_contents($this->prometheusConfigPath, Yaml::dump($config, 4, 2));
        } catch (\Exception $e) {
            Log::error('Errore nella scrittura di prometheus.yml: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'Impossibile scrivere il file di configurazione.');
        }

        // Ricarica Prometheus
        try {
            Http::post('http://localhost:9090/-/reload');
        } catch (\Exception $e) {
            Log::error('Failed to reload Prometheus: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'Cliente modificato, ma errore nel ricaricare Prometheus.');
        }

        return redirect()->route('customers.index')->with('success', 'Cliente modificato con successo.');
    }

    public function destroy($customer, $ip)
    {
        try {
            $config = Yaml::parseFile($this->prometheusConfigPath);
        } catch (\Exception $e) {
            Log::error('Errore nella lettura di prometheus.yml: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'Impossibile leggere il file di configurazione.');
        }

        foreach ($config['scrape_configs'] as &$scrapeConfig) {
            if (in_array($scrapeConfig['job_name'], ['mssql', 'windows_exporter'])) {
                $scrapeConfig['static_configs'] = array_filter($scrapeConfig['static_configs'], function ($staticConfig) use ($customer, $ip) {
                    $currentIp = str_replace([':4000', ':9182'], '', $staticConfig['targets'][0]);
                    return !($currentIp === $ip && $staticConfig['labels']['customer'] === $customer);
                });
            }
        }

        try {
            file_put_contents($this->prometheusConfigPath, Yaml::dump($config, 4, 2));
        } catch (\Exception $e) {
            Log::error('Errore nella scrittura di prometheus.yml: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'Impossibile scrivere il file di configurazione.');
        }

        // Ricarica Prometheus
        try {
            Http::post('http://localhost:9090/-/reload');
        } catch (\Exception $e) {
            Log::error('Failed to reload Prometheus: ' . $e->getMessage());
            return redirect()->route('customers.index')->with('error', 'Cliente eliminato, ma errore nel ricaricare Prometheus.');
        }

        return redirect()->route('customers.index')->with('success', 'Cliente eliminato con successo.');
    }
}

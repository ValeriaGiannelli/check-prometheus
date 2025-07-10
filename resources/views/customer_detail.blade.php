@extends('layouts.main')

@section('titlePage')
    Dettagli {{ $type === 'sql' ? 'SQL' : 'Servizi' }} per {{ $customer }} ({{ $instance }})
@endsection

@section('content')
    <div class="container my-5">
        <h1>Dettagli {{ $type === 'sql' ? 'SQL' : 'Servizi' }} per {{ $customer }} ({{ $instance }})</h1>
        <a href="{{ route('customer.metrics') }}" class="btn btn-sm btn-secondary mb-3">Torna alla panoramica</a>
        @if (!empty($metrics))
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Servizio</th>
                        <th>Valore</th>
                        <th>Stato</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($type === 'sql')
                        {{-- Stato di connessione --}}
                        @foreach ($metrics['connection'] as $metric)
                            <tr>
                                <td>{{ $metric['metric']['name'] ?? 'mssql_up' }}</td>
                                <td>{{ $metric['value'][1] }}</td>
                                <td>
                                    @if ($metric['value'][1] == '1')
                                        <span class="badge bg-success">In esecuzione</span>
                                    @else
                                        <span class="badge bg-danger">Spento</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        {{-- Utilizzo memoria --}}
                        @foreach ($metrics['memory'] as $metric)
                            <tr>
                                <td>{{ $metric['metric']['name'] ?? 'mssql_sql_memory_utilization_percentage' }}</td>
                                <td>{{ number_format($metric['value'][1], 2) }}%</td>
                                <td>
                                    <span class="badge {{ $metric['value'][1] > 80 ? 'bg-warning' : 'bg-info' }}">
                                        {{ $metric['value'][1] > 80 ? 'Allerta' : 'OK' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach

                        {{-- Spazio su disco --}}
                        @foreach ($metrics['disk'] as $metric)
                            <tr>
                                <td>{{ $metric['metric']['name'] ?? 'mssql_disk_usage_percent' }} ({{ $metric['metric']['database'] }})</td>
                                <td>{{ number_format($metric['value'][1], 2) }}%</td>
                                <td>
                                    <span class="badge bg-warning">Allerta</span>
                                </td>
                            </tr>
                        @endforeach

                    @elseif ($type === 'services')
                        {{-- servizi --}}
                        @foreach ($metrics['service'] as $metric)
                            @if($metric['value'][1] == '1')
                            <tr>
                                <td>{{ $metric['metric']['name'] }}</td>
                                <td>{{ $metric['metric']['instance'] ?? 'N/A' }}</td>
                                <td>
                                    @if($metric['metric']['state'] == 'stopped')
                                        <span class="badge bg-danger">Spento</span>
                                    @elseif($metric['metric']['state'] == 'running')
                                        <span class="badge bg-success">In esecuzione</span>
                                    @else
                                        <span class="badge bg-warning">{{ $metric['metric']['state'] }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
        @else
            <p>Nessun dato disponibile o errore nel recupero dei dati.</p>
        @endif
    </div>
@endsection
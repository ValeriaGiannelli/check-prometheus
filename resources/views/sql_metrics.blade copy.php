@extends('layouts.main')

@section('titlePage')
    Servizi SQL Server
@endsection

@section('content')
    <div class="container my-5">
        <h1>SQL Server Metrics</h1>
        @if (!empty($metrics['connection']) || !empty($metrics['memory']) || !empty($metrics['disk']))
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>SQL</th>
                        <th>Cliente</th>
                        <th>Stato</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Stato di connessione --}}
                    @foreach ($metrics['connection'] as $metric)
                        <tr>
                            <td>{{ $metric['metric']['name'] ?? 'SQL connesso' }}</td>
                            <td>{{ $metric['metric']['customer'] ?? 'N/A' }}</td>
                            <td>
                                @if ($metric['value'][1] == '1')
                                    <span class="badge bg-success">Connesso</span>
                                @else
                                    <span class="badge bg-danger">Spento</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    {{-- Utilizzo memoria --}}
                    @foreach ($metrics['memory'] as $metric)
                        <tr>
                            <td>{{ $metric['metric']['name'] ?? 'RAM in uso (%)' }}</td>
                            <td>{{ $metric['metric']['customer'] ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $metric['value'][1] > 80 ? 'bg-warning' : 'bg-info' }}">
                                    {{ number_format($metric['value'][1], 2) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach

                    {{-- Spazio su disco --}}
                    @foreach ($metrics['disk'] as $metric)
                        <tr>
                            <td>{{ $metric['metric']['name'] ?? 'Memoria in uso (%)' }}</td>
                            <td>{{ $metric['metric']['customer'] ?? 'N/A' }}</td>
                            <td>
                                @if($metric['value'][1] > 80)
                                    <span class="badge bg-danger">
                                        {{ number_format($metric['value'][1], 2) }}%
                                    </span>
                                @elseif($metric['value'][1] > 50)
                                <span class="badge bg-warning">
                                    {{ number_format($metric['value'][1], 2) }}%
                                </span>
                                @else
                                    <span class="badge bg-success">
                                        {{ number_format($metric['value'][1], 2) }}%
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No metrics available or error fetching data.</p>
        @endif
    </div>
@endsection
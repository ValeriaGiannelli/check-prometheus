@extends('layouts.main')

@section('titlePage')
    Versioni Software e Plugin
@endsection

@section('content')
    <div class="container my-5">
        <h1>Versioni</h1>
        {{-- @if (!empty($metrics['connection']) || !empty($metrics['memory']) || !empty($metrics['disk'])) --}}
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cliente</th>
                        <th>Versione</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Versione Software --}}
                    @foreach ($metrics['software'] as $metric)
                        <tr>
                            <td>{{ $metric['metric']['plugin_name'] ?? 'Software non trovato' }}</td>
                            <td>{{ $metric['metric']['customer'] ?? 'Cliente non trovato' }}</td>
                            @if($metric['metric']['version'] == '3.14.2')
                                <td>
                                    <span class="badge bg-success">
                                        {{ $metric['metric']['version'] ?? 'N/A' }}
                                    </span>
                                </td>
                            @else
                                <td>
                                    <span class="badge bg-warning">
                                        {{ $metric['metric']['version'] ?? 'N/A' }}
                                    </span>
                                </td>
                            @endif
                        </tr>
                    @endforeach

                    {{-- Versione plugin --}}
                    @foreach ($metrics['plugin'] as $metric)
                        <tr>
                            <td>{{ $metric['metric']['plugin_name'] ?? 'Software non trovato' }}</td>
                            <td>{{ $metric['metric']['customer'] ?? 'Cliente non trovato' }}</td>
                            <td>{{ $metric['metric']['version'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach

                    {{-- Spazio su disco --}}
                    {{-- @foreach ($metrics['disk'] as $metric)
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
                    @endforeach --}}
                </tbody>
            </table>
        {{-- @else
            <p>No metrics available or error fetching data.</p>
        @endif --}}
    </div>
@endsection
@extends('layouts.main')

@section('titlePage')
    Errori di Sistema
@endsection

@section('content')
    <div class="container my-5">
        <h1>Errori di Sistema</h1>
        @if ($logs->isNotEmpty())
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Ignora</th>
                        <th>Cliente</th>
                        <th>Descrizione Errore</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr data-log-id="{{ $log->id }}">
                            <td>
                                <input type="checkbox" class="ignore-checkbox" 
                                       data-log-id="{{ $log->id }}" 
                                       data-description="{{ htmlentities($log->description) }}" 
                                       data-client-id="{{ $log->client_id ?? '' }}"
                                       title="Marca come trascurabile">
                            </td>
                            <td>{{ $log->client_id ?? 'N/A' }}</td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Nessun errore disponibile.</p>
        @endif
        <script src="{{ asset('js/logs.js') }}"></script>
    </div>
@endsection
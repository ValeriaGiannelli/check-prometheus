{{-- questa view estende il file main.blade.php che è dentro la cartella view/layouts --}}
@extends('layouts.main')

@section('content')
    <div class="container my-5">
        <h1>Prometheus Metrics</h1>
        @if (!empty($metrics))

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Servizio</th>
                        <th>Instance</th>
                        <th>Stato</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($metrics as $metric)
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
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No metrics available or error fetching data.</p>
        @endif
    </div>
@endsection


@section('titlePage')
    servizi
@endsection

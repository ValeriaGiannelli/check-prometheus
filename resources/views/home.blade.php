{{-- questa view estende il file main.blade.php che è dentro la cartella view/layouts --}}
@extends('layouts.main')


@section('content')
    <div class="container my-5">
        <h1>Prometheus Metrics</h1>
        @if (!empty($metrics))

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Monitoraggio</th>
                        <th>Stato</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($metrics as $metric)
                        <tr>
                            <td>{{ $metric['metric']['customer'] ?? 'N/A' }}</td>
                            <td>{{ $metric['metric']['job'] ?? 'N/A' }}</td>
                            <td>
                                @if($metric['value'][1] == '1')
                                    <span class="badge bg-success">Up</span>
                                @else
                                    <span class="badge bg-danger">Down</span>
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


@section('titlePage')
    home
@endsection

{{-- questa view estende il file main.blade.php che è dentro la cartella view/layouts --}}
@extends('layouts.main')


@section('content')
    <div class="container my-5">
        <h1>Prometheus Metrics</h1>
        @if (!empty($metrics))
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Instance</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($metrics as $metric)
                        <tr>
                            <td>{{ $metric['metric']['__name__'] }}</td>
                            <td>{{ $metric['metric']['instance'] ?? 'N/A' }}</td>
                            <td>{{ $metric['value'][1] }}</td>
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

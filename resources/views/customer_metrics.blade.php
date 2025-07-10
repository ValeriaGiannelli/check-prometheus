@extends('layouts.main')

@section('titlePage')
    Monitoraggio Clienti
@endsection

@section('content')
    <div class="container my-5">
        <h1>Monitoraggio Clienti</h1>
        @if (!empty($customers))
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Istanza</th>
                        <th>SQL</th>
                        <th>Servizi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer => $data)
                        @foreach ($data['instances'] as $instance => $instanceData)
                            <tr>
                                <td>{{ $customer }}</td>
                                <td>{{ $instance }}</td>
                                <td>
                                    <div class="badge-container">
                                        <a href="{{ route('customer.detail', ['customer' => $customer, 'instance' => $instanceData['encoded_instance'], 'type' => 'sql']) }}" class="badge {{ $instanceData['sql_alerts'] > 0 ? 'bg-warning' : 'bg-success' }}">
                                            SQL
                                        </a>
                                        @if ($instanceData['sql_alerts'] > 0)
                                            <span class="alert-count">{{ $instanceData['sql_alerts'] }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="badge-container">
                                        <a href="{{ route('customer.detail', ['customer' => $customer, 'instance' => $instanceData['encoded_instance'], 'type' => 'services']) }}" class="badge {{ $instanceData['service_alerts'] > 0 ? 'bg-warning' : 'bg-success' }}">
                                            Servizi
                                        </a>
                                        @if ($instanceData['service_alerts'] > 0)
                                            <span class="alert-count">{{ $instanceData['service_alerts'] }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Nessun dato disponibile o errore nel recupero dei dati.</p>
        @endif
    </div>
@endsection
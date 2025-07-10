@extends('layouts.main')

@section('titlePage')
    Gestione Clienti
@endsection

@section('content')
    <div class="container my-5">
        <h1>Gestione Clienti</h1>
        <a href="{{ route('customers.create') }}" class="btn btn-primary mb-3">Aggiungi Cliente</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (isset($error))
            <div class="alert alert-danger">{{ $error }}</div>
        @endif
        @if (!empty($customers))
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>IP</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer => $instances)
                        @foreach ($instances as $instance => $data)
                            <tr>
                                <td>{{ $customer }}</td>
                                <td>{{ str_replace(':1433', '', $instance) }}</td>
                                <td>
                                    <a href="{{ route('customers.edit', ['customer' => $customer, 'ip' => str_replace(':1433', '', $instance)]) }}" class="btn btn-sm btn-warning">Modifica</a>
                                    <form action="{{ route('customers.destroy', ['customer' => $customer, 'ip' => str_replace(':1433', '', $instance)]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo cliente?')">Elimina</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Nessun cliente configurato.</p>
        @endif
    </div>
@endsection
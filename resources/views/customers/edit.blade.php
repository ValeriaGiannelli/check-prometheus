@extends('layouts.main')

@section('titlePage')
    Modifica Cliente
@endsection

@section('content')
    <div class="container my-5">
        <h1>Modifica Cliente: {{ $customer }}</h1>
        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary mb-3">Torna all'elenco</a>
        <form action="{{ route('customers.update', ['customer' => $customer, 'ip' => $ip]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="customer" class="form-label">Nome Cliente</label>
                <input type="text" name="customer" id="customer" class="form-control @error('customer') is-invalid @enderror" value="{{ $customer }}">
                @error('customer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="ip" class="form-label">Indirizzo IP</label>
                <input type="text" name="ip" id="ip" class="form-control @error('ip') is-invalid @enderror" value="{{ $ip }}">
                @error('ip')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Salva</button>
        </form>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Lista de DVDs')

@section('content')
<div class="container mt-4">
    <h1 class="text-left mb-4">Lista de DVDs</h1>
    <div class="row">
        @foreach($dvds as $dvd)
            <div class="col-md-3">
                <a href="{{ $dvd['url'] }}" target="_blank">
                    <div class="card mb-4 shadow-sm">
                        <img src="{{ $dvd['cover'] }}" class="card-img-top" alt="{{ $dvd['title'] }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $dvd['title'] }}</h5>
                            <p class="card-text">Preço: {{ $dvd['price'] }}</p>
                            <a href="{{ $dvd['url'] }}" class="btn btn-primary col" target="_blank">Ver Detalhes</a>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection

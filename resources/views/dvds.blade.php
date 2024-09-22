@extends('layouts.app')

@section('title', 'Lista de DVDs')

@section('content')
<div class="container mt-4">

    <div class="jumbotron text-center mb-4" style="background-image: url('/img/banner.jpg'); background-size: cover; background-attachment: fixed;">
        <h1 class="display-4 text-white text-shadow">Bem-vindo ao CineFetch</h1>
        <p class="lead text-white text-shadow">lorem</p>
        <hr class="my-4">
        <p class="text-white">Use os filtros abaixo para encontrar o que você procura!</p>
    </div>

    <form method="GET" action="{{ url('dvds') }}" class="mb-4">
        <div class="form-row border rounded p-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar por título" value="{{ request()->input('search') }}">
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" name="min_price" class="form-control" placeholder="Preço mínimo" value="{{ request()->input('min_price') }}">
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" name="max_price" class="form-control" placeholder="Preço máximo" value="{{ request()->input('max_price') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
            </div>
        </div>
        <div class="form-row mt-3">
            <div class="col-md-3">
                <select name="sort" class="form-control ml-auto" onchange="this.form.submit()">
                    <option value="">Ordenar por</option>
                    <option value="asc" {{ request()->input('sort') == 'asc' ? 'selected' : '' }}>Menor Preço</option>
                    <option value="desc" {{ request()->input('sort') == 'desc' ? 'selected' : '' }}>Maior Preço</option>
                </select>
            </div>
        </div>
    </form>

    <h3 class="text-left mb-3 mt-5">Lista de DVDs</h3>

    <div class="row">
        @foreach($dvds as $dvd)
            <div class="col-md-3">
                <a href="{{ $dvd['url'] }}" target="_blank">
                    <div class="card mb-4 shadow rounded border-0">
                        <img src="{{ $dvd['cover'] }}" class="card-img-top" alt="{{ $dvd['title'] }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $dvd['title'] }}</h5>
                            <p class="card-text">Preço: {{ $dvd['price'] }}</p>
                            <a href="{{ $dvd['url'] }}" class="btn btn-primary col mt-3" target="_blank">Ver Detalhes</a>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection

{{--@extends('layouts.app')--}}

{{--@section('content')--}}
    <div class="container">
        <h1>Список лидов</h1>
        <ul>
            @foreach ($leads as $lead)
                <li>#{{ $lead->id }} - {{ $lead->name ?? 'Без названия' }}</li>
            @endforeach
        </ul>
    </div>
{{--@endsection--}}

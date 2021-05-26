@extends('_layouts.base')

@section('body')
    <body>
    <div class="card">
        <div class="body">
            <h1>Deinem User "{{Illuminate\Support\Facades\Auth::user()->id}} {{Illuminate\Support\Facades\Auth::user()->first_name}} {{Illuminate\Support\Facades\Auth::user()->name}}" ist keine Gliederung zugewiesen.</h1>
        </div>
    </div>
    <div class="pull-right top-buffer">
        <a href="/impressum">Impressum</a>
        <a href="/datenschutz">Datenschutz</a>
    </div>
    </body>
@endsection
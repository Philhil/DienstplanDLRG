@extends('_layouts.base')

@section('body')
    <body>
        <div class="card">
            <div class="body">
                <h1>Impressum</h1>

                <p>{{env('impressum.name')}}
                <br />{{env('impressum.street')}} {{env('impressum.number')}}
                <br />{{env('impressum.zip')}} {{env('impressum.city')}}</p>
                <p>Telefon: {{ env('impressum.phone') }}</p>
                <p>E-Mail: {{ env('impressum.mail') }}</p>
                @if(env('impressum.UmsatzsteuerIdentifikationsnummer') != null)
                <p>Umsatzsteuer-Identifikationsnummer gem. &sect; 27a UStG: {{env('impressum.UmsatzsteuerIdentifikationsnummer')}}</p>
                @endif
                <p>Inhaltlich Verantwortlicher gem. &sect; 55 II RStV: {{env('impressum.name')}} (Anschrift s.o.)</p>

            </div>
        </div>
        <div class="pull-right top-buffer">
            <a href="/impressum">Impressum</a>
            <a href="/datenschutz">Datenschutz</a>
        </div>
    </body>
@endsection
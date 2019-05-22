@extends('_layouts.base')

@section('body')
    <body>
        <div class="card">
            <div class="body">
                <h1>Datenschutzerklärung</h1>

                <p>Verantwortlicher für die Erhebung, Verarbeitung und Nutzung Ihrer personenbezogenen Daten im Sinne von Art. 4 Nr. 7 DSGVO ist</p>

                <p>{{env('impressum.name')}}
                <br />{{env('impressum.street')}} {{env('impressum.number')}}
                <br />{{env('impressum.zip')}} {{env('impressum.city')}}</p>
                <p>E-Mail: {{ env('impressum.mail') }}</p>

                <h2>Betroffenenrechte: Rechte auf Auskunft, Berichtigung, Sperre, Löschung und  Widerspruch</h2>
                <p>Sie haben das Recht, auf Antrag unentgeltlich eine Auskunft über die bei uns  gespeicherten personenbezogenen Daten anzufordern und/oder eine Berichtigung,  Sperrung oder Löschung zu verlangen. Eine Sperrung oder Löschung kann nicht  erfolgen, wenn gesetzliche Regelungen dem entgegenstehen.</p>
                <p>Bitte kontaktieren Sie unseren Datenschutzbeauftragten unter  <a class="moz-txt-link-abbreviated">{{ env('impressum.mail') }}</a>.</p>
                <h2>Datenvermeidung und Datensparsamkeit</h2>
                <p>Wir speichern gemäß den Grundsätzen der Datenvermeidung und Datensparsamkeit  personenbezogene Daten nur so lange, wie dies erforderlich ist oder vom  Gesetzgeber vorgeschrieben wird.</p>
                <h2>Erfassung allgemeiner Informationen</h2>
                <p>Für  administrative Zwecke kann eine Erstellung von Logfiles notwendig  werden. Die Logfiles werden für maximal eine Woche gespeichert. Die Logfiles  sind von allgemeiner Natur und erlauben keine Rückschlüsse auf Ihre Person.</p>
                <p>Sofern Logfiles erstellt werden, können unter anderem folgende Datenarten  erfasst werden: IP-Adresse und Datum.</p>

            </div>
        </div>
        <div class="pull-right top-buffer">
            <a href="/impressum">Impressum</a>
            <a href="/datenschutz">Datenschutz</a>
        </div>
    </body>
@endsection
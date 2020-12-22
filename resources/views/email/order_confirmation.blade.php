<h1>Bestellbestätigung</h1>

Hallo {{$user->first_name}} {{$user->name}},
<br>
hiermit bestätigen wir die Bestellung des neuen Dienstplans für die Gliederung {{$client->name}}.
Die Gliederung und der Benutzeraccount wurden bereits angelegt. Sobald der Nutzer von uns freigegeben wurde, bekommen Sie noch eine seperate E-Mail und können direkt loslegen.
Da die Freigabe manuell durchgeführt wird, bitten wir Sie um etwas Geduld.

<h3>Bestätigung der Gliederungsdaten:</h3>
Name: {{$client->name}}<br>
Rechnungsadresse: {{$order['client_billing']}} <br>
Gliederungsanschrift: {{$order['client_billing_address']}} <br>

Gebuchtes Paket: {{$order["package"]}} <br> //TODO Paketbeschreibung
Aktivierte Module: <br>
@if(in_array('module_default', $order))- Modul Dienstplan + Grundfunktionen <br> @endif
@if(in_array('module_training', $order))- Modul Training <br> @endif
@if(in_array('module_stats', $order))- Modul Erweiterte Analyse und Auswertungen <br> @endif


<h3>Weiteres Vorgehen:</h3>
- Prüfen der Einstellungen: <a href="{{action('ClientController@show', $client->id)}}">{{action('ClientController@show', $client->id)}}</a><br>
- Qualifikationen anlegen: <a href="{{ action('QualificationController@index') }}">{{ action('QualificationController@index') }}</a><br>
- Und schon kann es losgehen: Sie können nun Ihre Dienste eigenhändig im System anlegen und Helfer können sich auf der Startseite selbst registrieren.<br>


<h3>Benutzerhandbuch:</h3>
In unserem Benutzerhandbuch haben wir alle Funktionen im Detail beschrieben: <a href="{{action('HomeController@getUserGuide')}}">{{action('HomeController@getUserGuide')}}</a>
<br>
Sollten noch irgendwo offene Fragen oder Verbesserungsvorschläge auftauchen, stehen wir sehr gerne unter <a href="mailto:{{env('impressum.mail')}}?subject=Dienstplan%20Anfrage&amp;body=Hallo%20,">{{env('impressum.mail')}}</a> zur Verfügung.
<br>
<br>
Viele Grüße, {{env('impressum.name')}} <br>
<a href="https://dlrgdienstplan.de">https://dlrgdienstplan.de</a>
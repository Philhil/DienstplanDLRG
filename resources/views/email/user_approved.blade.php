Hallo {{$user->first_name}},
<br>
Dein Account beim <a href="{{action('HomeController@index')}}">DLRG Dienst Portal</a> wurde soeben für die Gliederung {{$client->name}} freigeschaltet.
<br>Du kannst dich nun dort mit Deiner E-Mail Adresse anmelden.
<br><br>
Viele Grüße,<br>
@if(isset($authorizedby)){{$authorizedby->first_name}}@endif
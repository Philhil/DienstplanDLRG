Hallo {{$user->first_name}},

dein Account beim <a href="{{action('HomeController@index')}}">DLRG Dienst Portal</a> wurde soeben für {{$client->name}} freigeschaltet.
<br>Du kannst dich nun dort mit deiner E-Mail Adresse anmelden.
<br><br>
Viele Grüße<br>
@if(isset($authorizedby)){{$authorizedby->first_name}}@endif
Hallo {{$user->first_name}},

dein Account beim <a href="{{action('HomeController@index')}}">DLRG Dienst Portal</a> wurde soeben freigeschaltet.
Du kannst dich nun dort mit deiner E-Mail Adresse einloggen.
<br><br>
Viele Grüße<br>
@if(isset($authorizedby)){{$authorizedby->first_name}}@endif
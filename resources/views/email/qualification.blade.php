Hallo {{$user->first_name}},

<br><br>
Dir wurde soeben die Qualifikation {{$qualification->name}} zugeteilt.
<br><br><br>
Viele Grüße,<br>
@if(isset($authorizedby)){{$authorizedby->first_name}}@endif
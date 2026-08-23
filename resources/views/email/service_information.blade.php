Hallo {{$user->first_name}},
<br>
Hier sind weitere Informationen zu deinem Dienst am <b>{{$position->service->date->isoFormat('ddd  DD.MM.YY H:mm')}} Uhr</b>:

{!! html_entity_decode($information->content) !!}

<br><br>
Viele Grüße,<br>
@if(isset($informedby))
{{$informedby->first_name}}
@endif

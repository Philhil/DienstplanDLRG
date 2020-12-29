Neue Helfermeldung:<br>
{{$position->service->date->isoFormat('ddd  DD.MM.YY H:mm')}} Uhr @if(!empty($position->service->dateEnd)) - {{$position->service->dateEnd->isoFormat('DD.MM.YY H:mm')}} Uhr @endif @if(!empty($position->service->comment))({{$position->service->comment}})@endif  <br>
{{$position->qualification->name}} <br>
{{$user->first_name}} {{$user->name}} <br>
<br>Freigeben: <a href="{{action('PositionController@authorizePos', $positionCandidature->id)}}">{{action('PositionController@authorizePos', $positionCandidature->id)}}</a>
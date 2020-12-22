Neue Helfermeldung:<br>
{{$position->service->date->format('d M Y')}} @if(!empty($position->service->comment))({{$position->service->comment}})@endif  <br>
{{$position->qualification->name}} <br>
{{$user->first_name}} {{$user->name}} <br>
<br>Freigeben: <a href="{{action('PositionController@authorizePos', $positionCandidature->id)}}">{{action('PositionController@authorizePos', $positionCandidature->id)}}</a>
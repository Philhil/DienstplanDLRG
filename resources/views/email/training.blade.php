<!DOCTYPE html>
<html>
<head>
    <title>{{$training->title}}</title>
</head>
<body>
<h1>{{$training->title}}</h1>
<smal>{{$training->date->format('l d.m.Y H:i')}} @if(!empty($training->dateEnd)) - {{$training->dateEnd->format('l d.m.Y H:i')}}@endif</smal>
<br>

{!! html_entity_decode($training->content) !!}

<p>Viele Gr&uuml;&szlig;e Dienstplan {{$training->client->name}}</p>
<p><a href="https://dlrgdienstplan.de/">https://dlrgdienstplan.de</a></p>
</body>
</html>
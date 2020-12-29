Hallo {{$position->user->first_name}},
<br>
Du wurdest soeben für den Dienst am <b>{{$position->service->date->isoFormat('ddd  DD.MM.YY H:mm')}} Uhr @if(!empty($position->service->dateEnd)) - {{$position->service->dateEnd->isoFormat('DD.MM.YY H:mm')}} Uhr @endif</b> @if(!empty($position->service->comment))({{$position->service->comment}})@endif als {{$position->qualification->name}} zugeteilt.

<br><br>
Bisher sind mit Dir eingetragen:<br>
<table>
    <tbody>
    @foreach($servicepositions as $pos)
        <tr>
            <td>{{$pos->qualification->name}}:</td>
            <td>{{$pos->user->first_name}} {{$pos->user->name}}</td>
            <td>@if(!empty($pos->comment))({{$pos->comment}})@endif</td>
        </tr>
    @endforeach
    </tbody>
</table>

<br><br>
Viele Grüße,<br>
@if(isset($authorizedby)){{$authorizedby->first_name}}@endif
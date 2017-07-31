Hallo {{$position->user->first_name}},

du wurdest soeben für den Dienst am <b>{{$position->service->date->format('d M Y')}}</b> @if(!empty($position->service->comment))({{$position->service->comment}})@endif als {{$position->qualification->name}} zugeteilt.

<br><br>
Bisher sind mit dir eingetragen:<br>
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
Viele Grüße<br>
@if(isset($authorizedby)){{$authorizedby->first_name}}@endif
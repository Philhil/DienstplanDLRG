<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            size: 42cm 29.7cm;
            margin: 5mm 2mm 5mm 2mm;
        }
        body {
            <!-- A3 -->
            width: 42cm;
            height: 29.7cm;
            font-family: Verdana, Tahoma, "DejaVu Sans", sans-serif;
        }
        .nopos{
            background-color: rgba(129, 127, 127, 0.5);
        }
        .notassigned {
            background-color: #c7bb00;
        }
        .assigned {
            background-color: #ffff00;
        }
        .open_required {
            background-color: #ff0000;
        }
        .open_optional {
            background-color: rgba(255, 0, 0, 0.7);
        }

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 15px;
        }

    </style>
</head>
<?php
foreach ($services as $service)
    {
        foreach ($service->positions as $position)
        {
            if (!$tableheader->contains($position->qualification))
            {
                $tableheader->push($position->qualification);
            }
        }
    }
?>
<body>
{{$client->name}} - Stand: {{Carbon\Carbon::now()->isoFormat('ddd  DD.MM.YY H:mm')}} Uhr
<table style="width:100%">
        <tr>
            <th>Datum</th>
            @foreach($tableheader as $qualification)
            <th>{{$qualification->name}}</th>
            @endforeach
            <th>Bemerkung</th>
        </tr>

        @foreach($services as $service)
            <tr>
                <td>{{$service->date->isoFormat('ddd  DD.MM.YY H:mm')}} Uhr @if(!empty($service->dateEnd)) - {{$service->dateEnd->isoFormat('DD.MM.YY H:mm')}} Uhr @endif</td>
                @foreach($tableheader as $qualification)
                    <?php $positionswithqualification = $service->positionwithQualification($qualification->id)->with('user')->get()  ?>

                    <td class="{{$positionswithqualification->isEmpty() ? "nopos" : ""}}">
                        @foreach($positionswithqualification as $position)
                            {{-- for different colors required/optional position
                             <div class="{{is_null($position->user_id) ? ($position->requiredposition == 1 ? "open_required" : "open_optional") : ""}}">
                             --}}
                            <div class="{{is_null($position->user_id) ? "open_required" : ""}}">
                            @if(is_null($position->user_id))
                                    ➤ frei
                            @else
                                    @if($client->isMailinglistCommunication == 0)
                                        {{-- if this is send directly, names can be shown. via a open mailinglist -> hide names because of privacy reasons --}}
                                        {{substr ($position->user->first_name, 0, 1)}}. {{$position->user->name}}
                                    @else
                                        ✖ belegt
                                    @endif
                            @endif
                            @if(!empty($position->comment))
                                <small>({{$position->comment}})</small>
                            @endif
                            </div>
                        @endforeach
                    </td>
                @endforeach
                <td>{{$service->comment}}</td>
            </tr>
        @endforeach
</table>
</body>
</html>
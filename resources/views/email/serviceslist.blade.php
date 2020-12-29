<style>
    @page {
        size: 42cm 29.7cm;
        margin: 5mm 2mm 5mm 2mm;
    }
    body {
        width: 42cm;
        height: 29.7cm;
    }
    .nopos{
        background-color: #353535;
    }
    .notassigned {
        background-color: #c7bb00;
    }
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 15px;
    }

</style>
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
Stand: {{Carbon\Carbon::now()->isoFormat('ddd  DD.MM.YY H:mm')}} Uhr
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
                            <div class="{{is_null($position->user_id) ? "notassigned" : ""}}">
                            @if(is_null($position->user_id))
                                    -
                            @else
                                {{substr ($position->user->first_name, 0, 1)}}. {{$position->user->name}}
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
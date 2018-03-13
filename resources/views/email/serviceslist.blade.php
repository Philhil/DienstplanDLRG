<style>
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
                <td>{{$service->date->format('d.m.Y')}}</td>
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
                            @if(isset($position->comment))
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
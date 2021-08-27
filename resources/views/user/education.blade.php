@if(\Illuminate\Support\Facades\Auth::user()->currentclient()->module_training_credit)
    <h3>Fortbildungspunkte</h3>

    <div class="body table-responsive">
        <table data-toggle="table" class="table table-striped table-hover" data-show-toggle="true"
               data-cookie="true" data-cookie-id-table="userprofileQualiView"
               data-show-export="true"
               @if(!Browser::isDesktop())
               data-card-view="true"
                @endif
        >
            <thead>
            <tr>
                <th data-sortable="true" data-field="Qualifikation" data-filter-control="input">↓ Quali | Saison →</th>
                @foreach($years as $year)
                <th data-sortable="true" data-field="{{$year}}">{{$year}}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>

            @foreach($all_qualfications_where_trainings_exsist_and_user_has as $quali_id => $quali_name)
                <tr>
                    <td>{{$quali_name}}</td>
                    @foreach($years as $year)
                        <td>
                            @if(array_key_exists($quali_id, $qualfication_credits[$year]))
                                {{$qualfication_credits[$year][$quali_id]}}
                            @else
                                0
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

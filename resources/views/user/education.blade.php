@if(\Illuminate\Support\Facades\Auth::user()->currentclient()->module_training_credit)
    <h3>Fortbildungspunkte</h3>

    @foreach($all_qualfications_where_trainings_exsist_and_user_has as $quali_id => $quali_name)
        <div class="col-sm-12">
            <b>{{$quali_name}}:
                <span class="badge bg-orange">
                @if(array_key_exists($quali_id, $qualfication_credits))
                        {{$qualfication_credits[$quali_id]}}
                    @else
                        0
                    @endif
                </span>
            </b>
        </div>
    @endforeach

@endif

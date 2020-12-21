@if(\Illuminate\Support\Facades\Auth::user()->currentclient()->module_training_credit)
    @foreach($qualifications as $qualification)
        <div class="col-sm-12">
            <b>{{$qualification->name}}: <span class="badge bg-orange">
                @if(array_key_exists($qualification->name, $qualfication_credits))
                    {{$qualfication_credits[$qualification->name]}}
                @else
                    0
                @endif
                </span>
            </b>
        </div>
    @endforeach
@endif

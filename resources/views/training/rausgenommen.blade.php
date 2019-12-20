<!--


 muss in training.index in Zeile 24


@if(\Illuminate\Support\Facades\Auth::user()->isAdmin())
    <ul class="header-dropdown m-r--5">
        <li class="dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="material-icons">more_vert</i>
            </a>
           <ul class="dropdown-menu pull-right">
                <li><a href="{{action('ServiceController@editTraining', $training->id) }}" class=" waves-effect waves-block"><i class="material-icons">mode_edit</i>Bearbeiten</a></li>
                                    <li><a href="{{action('ServiceController@delete', $training->id) }}" class=" waves-effect waves-block"><i class="material-icons">delete</i> Löschen</a></li>
                                </ul>
                            </li>
                        </ul>
                    @endif


-->
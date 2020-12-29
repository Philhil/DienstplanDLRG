@extends('_layouts.application')

@section('head')
@endsection

@section('content')

    @foreach($newss as $news)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-blue-grey">
                    <h2>
                        {{$news->title}} <small>{{$news->created_at->isoFormat('ddd  DD.MM.YY H:mm')}} @if(isset($news->user))von {{$news->user->first_name}} {{$news->user->name}} @endif</small>
                    </h2>
                    @if(\Illuminate\Support\Facades\Auth::user()->isAdmin())
                        <ul class="header-dropdown m-r--5">
                            <li class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="material-icons">more_vert</i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="{{ action('NewsController@edit', $news->id) }}" class=" waves-effect waves-block"><i class="material-icons">mode_edit</i>Bearbeiten</a></li>
                                    <li><a href="{{ action('NewsController@delete', $news->id) }}" class=" waves-effect waves-block"><i class="material-icons">delete</i> Löschen</a></li>
                                </ul>
                            </li>
                        </ul>
                    @endif
                </div>
                <div class="body">
                    {!! $news->content !!}
                </div>
            </div>
        </div>
    @endforeach

@endsection

@section('post_body')
    <script>
        $( document ).ready(function() {

        });
    </script>
@endsection
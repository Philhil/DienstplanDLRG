@extends('_layouts.application')

@section('head')

@endsection

@section('content')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header bg-green">
                <h2>Abfragen</h2>
                <small>Abfragen werden bei jedem Helfer solange präsend dargestellt, bis diese bestätigt wurden.</small>

                @if($isAdmin)
                <ul class="header-dropdown m-r--5">
                    <a href="{{action('SurveyController@create')}}">
                        <button type="button" class="btn bg-grey waves-effect">
                            <i class="material-icons">playlist_add</i>
                            <span>Abfrage Starten</span>
                        </button>
                    </a>
                </ul>
                @endif
            </div>

            <div class="body table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Start</th>
                        <th>Ende</th>
                        <th>an Nutzer</th>
                        <th>Verpflichtend</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($surveys as $survey)
                        <tr>
                            <td>{{$survey->title}}</td>
                            <td>@if($survey->dateStart){{$survey->dateStart->isoFormat('ddd DD.MM.YY HH:mm')}}@endif</td>
                            <td>@if($survey->dateEnd){{$survey->dateEnd->isoFormat('ddd DD.MM.YY HH:mm')}}@endif</td>
                            <td>@if($survey->qualification){{$survey->qualification->short}}@else Alle Nutzer @endif</td>
                            <td>@if($survey->mandatory)✓@else✗@endif</td>
                            <td>
                                <a href="/survey/{{$survey->id}}">
                                    <button type="button" class="btn btn-success waves-effect">
                                        <i class="material-icons">remove_red_eye</i>
                                    </button>
                                </a>
                                <a href="/survey/{{$survey->id}}/edit">
                                    <button type="button" class="btn btn-warning waves-effect">
                                        <i class="material-icons">mode_edit</i>
                                    </button>
                                </a>
                                {{ Form::open(['url' => '/survey/'.$survey->id, 'method' => 'delete', 'style'=>'display:inline-block']) }}
                                <button type="submit" class="btn btn-danger waves-effect btn-delete">
                                    <i class="material-icons">delete</i>
                                </button>
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-felx justify-content-center">
                    {{ $surveys->links() }}
                </div>

            </div>

        </div>
    </div>

@endsection

@section('post_body')
    <script>
        $(document).ready(function () {

        });
    </script>
@endsection
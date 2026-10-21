@extends('_layouts.application')

@section('head')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-blue-grey">
                    <h2>Wachen <small>Anwesenheitslisten behalten die Wache auch wenn sie gelöscht wird</small></h2>
                </div>
                <div class="body table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr><th>Name</th><th>Aktion</th></tr>
                        </thead>
                        <tbody>
                        @foreach($stations as $station)
                            <tr>
                                <td>
                                    <form method="POST" action="{{ action('StationController@update', $station->id) }}" class="form-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" class="form-control" maxlength="255" required value="{{ $station->name }}">
                                        <button type="submit" class="btn btn-success waves-effect"><i class="material-icons">save</i></button>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="{{ action('StationController@destroy', $station->id) }}" onsubmit="return confirm('Wache wirklich löschen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger waves-effect"><i class="material-icons">delete</i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($stations->count() == 0)
                            <tr><td colspan="2">Keine Wachen angelegt. Ohne Wachen wird das Feld bei Einsätzen nicht angezeigt.</td></tr>
                        @endif
                        </tbody>
                    </table>

                    <form method="POST" action="{{ action('StationController@store') }}" class="form-inline">
                        @csrf
                        <input type="text" name="name" class="form-control" maxlength="255" required placeholder="Neue Wache...">
                        <button type="submit" class="btn btn-success waves-effect"><i class="material-icons">add</i> Wache anlegen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

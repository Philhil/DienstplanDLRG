@extends('_layouts.application')

@section('head')
    <!-- Bootstrap Select Css -->
    <link href="/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Input -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        {{$user->first_name}} {{$user->name}}
                    </h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            @include('user._form')
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            @include('user.education')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- #END# Input -->

        @if(Auth::id() == $user->id)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Kalender abonnieren</h2>
                </div>
                <div class="body">
                    <p>Was soll im iCal-Kalenderabo enthalten sein?</p>
                    <div style="margin-bottom: 16px;">
                        <div class="checkbox" style="display: inline-block; margin-right: 24px;">
                            <input type="checkbox" id="toggle-services" class="filled-in chk-col-red" checked onchange="ensureOneChecked('toggle-trainings', this)">
                            <label for="toggle-services">Dienste</label>
                        </div>
                        <div class="checkbox" style="display: inline-block;">
                            <input type="checkbox" id="toggle-trainings" class="filled-in chk-col-red" checked onchange="ensureOneChecked('toggle-services', this)">
                            <label for="toggle-trainings">Übungen</label>
                        </div>
                    </div>
                    <button class="btn btn-danger waves-effect" type="button" onclick="generateIcalUrl()">
                        <i class="material-icons">link</i> Link generieren
                    </button>

                    <button class="btn btn-default waves-effect" type="button" style="margin-left: 8px;" data-toggle="modal" data-target="#revokeIcalModal">
                        <i class="material-icons">refresh</i> Link zurücksetzen
                    </button>

                    <div id="ical-result" style="display: none; margin-top: 16px;">
                        <p class="text-muted" style="font-size: 0.9em;">
                            Kopiere diese URL in deine Kalender-App. Meldungen für Dienste erscheinen mit dem Zusatz <em>(nicht bestätigt)</em> im Titel.
                        </p>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input type="text" id="ical-url" class="form-control" readonly style="flex: 1;">
                            <button class="btn btn-default waves-effect" type="button" onclick="copyIcalUrl()" title="Kopieren" style="white-space: nowrap; flex-shrink: 0;">
                                <i class="material-icons">content_copy</i> Kopieren
                            </button>
                        </div>
                        <span id="ical-copy-feedback" style="display:none; color: green; font-size: 0.85em; margin-top: 4px;">
                            <i class="material-icons" style="font-size:1em; vertical-align: middle;">check</i> URL kopiert!
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if(Auth::id() == $user->id)
    <div class="modal fade" id="revokeIcalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Kalender-Link zurücksetzen?</h4>
                </div>
                <div class="modal-body">
                    <p>Der aktuelle Link wird <strong>sofort ungültig</strong>. Alle Kalender-Apps, die diesen Link abonniert haben, erhalten keine Updates mehr.</p>
                    <p class="text-muted" style="font-size: 0.9em;">Du bekommst danach einen neuen Link, den du neu abonnieren musst.</p>
                </div>
                <div class="modal-footer">
                    <form method="POST" action="{{ route('user.revokeIcalToken', $user->id) }}">
                        @csrf
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-danger waves-effect">Zurücksetzen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection


@section('post_body')

    <script>
        function ensureOneChecked(otherId, changed) {
            if (!changed.checked && !document.getElementById(otherId).checked) {
                document.getElementById(otherId).checked = true;
            }
        }

        function generateIcalUrl() {
            var services  = document.getElementById('toggle-services').checked  ? 'true' : 'false';
            var trainings = document.getElementById('toggle-trainings').checked ? 'true' : 'false';
            var base = '{{ route("ical.feed", $user->ical_token) }}';
            var url = base + '?services=' + services + '&trainings=' + trainings;
            document.getElementById('ical-url').value = url;
            document.getElementById('ical-result').style.display = 'block';
        }

        function copyIcalUrl() {
            var url = document.getElementById('ical-url').value;
            navigator.clipboard.writeText(url).then(function() {
                var feedback = document.getElementById('ical-copy-feedback');
                feedback.style.display = 'inline';
                setTimeout(function() { feedback.style.display = 'none'; }, 2500);
            });
        }
    </script>
@endsection

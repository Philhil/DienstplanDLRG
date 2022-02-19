@if(Session::get('survey') != null)
<script>
    $( document ).ready(function() {
        $('#surveyModal').modal('toggle');
    });
</script>

<!-- surveyModal -->
<div class="modal fade" id="surveyModal" tabindex="-1" role="dialog"  data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="smallModalLabel">Abfrage ausstehend</h4>
            </div>
            <div class="modal-body">
                Hallo {{\Illuminate\Support\Facades\Auth::user()->first_name}}, <br>
                du hast eine offene Abfrage: <b>{{Session::get('survey_title')}}</b>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect bg-green" onclick="window.location='{{ route("survey.show", Session::get('survey')) }}'">Abfrage öffnen</button>
                <button type="button" class="btn btn-link waves-effect" onclick="window.location='{{ route("survey.postpone", Session::get('survey')) }}'">morgen erinnern</button>
            </div>
        </div>
    </div>
</div>
    @php
        Session::forget('survey');
        Session::forget('survey_title');
    @endphp
@endif
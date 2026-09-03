{{ html()->form('POST', action('ServiceController@storeServiceInformation', $serviceInformation->service_id))->open() }}
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <!-- TinyMCE -->
        <div class="row clearfix">
            <div class="form-group {{ $errors->has('content') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ html()->textarea('content', old('content', $serviceInformation->content))->class('form-control')->id('tinymce') }}
                    {!! $errors->first('content', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <!-- #END# TinyMCE -->
    </div>

    <div class="row clearfix">
        <div class="col-sm-1 pull-right">
            <div class="form-line">
                {{ html()->submit('Speichern')->class('form-control btn btn-success waves-effect') }}
            </div>
        </div>
    </div>
{{ html()->form()->close() }}


@section('post_body')

    <!-- TinyMCE -->
    <script src="/plugins/tinymce/tinymce.js"></script>

    <script>
        $( document ).ready(function() {
            //TinyMCE
            tinymce.init({
                selector: "textarea#tinymce",
                theme: "modern",
                height: 300,
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste textcolor colorpicker textpattern imagetools'
                ],
                toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                toolbar2: 'print preview media | forecolor backcolor emoticons',
                image_advtab: true
            });
            tinymce.suffix = ".min";
            tinyMCE.baseURL = '/plugins/tinymce';
        });
    </script>
@endsection

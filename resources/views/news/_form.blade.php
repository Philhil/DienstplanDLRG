@if(\Illuminate\Support\Facades\Route::current()->getName() == 'news.edit')
    {{ Form::model($news, ['action' => ['NewsController@update', 'id' => $news->id], 'method' => 'PUT']) }}
@else
    {{ Form::model($news, ['action' => ['NewsController@store', 'id' => $news->id]]) }}
@endif

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        <div class="row clearfix">
            <div class="col-sm-10">
                <div class="form-group {{ $errors->has('comment') ? 'has-error' : ''}}">
                    <div class="form-line">
                        {{ Form::label('title', 'Titel:') }}
                        {{ Form::text('title', old('title'), ['placeholder' => "Titel...", 'class' => 'form-control no-resize']) }}
                        {!! $errors->first('title', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- TinyMCE -->
        <div class="row clearfix">
            <div class="form-group {{ $errors->has('content') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::textarea('content', old('content'), ['class' => 'form-control', 'id'=>"tinymce"]) }}
                    {!! $errors->first('content', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <!-- #END# TinyMCE -->
    </div>

</div>

<div class="row clearfix">
    <div class="col-sm-1 pull-right">
        <div class="form-line">
            {{ Form::button('Speichern', ['class' => 'form-control btn btn-success waves-effect', 'type' => "submit"]) }}
        </div>
    </div>
</div>
{{ Form::close() }}


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
{{-- messaggi vari --}}
<?php $message = Session::get('message'); ?>
@if( isset($message) )
<div class="alert alert-success">{{$message}}</div>
@endif

<h3>Aggiungi nuova categoria</h3>
{{Form::model($categories, ['url' => URL::action('Palmabit\Catalog\Controllers\CategoryController@postEdit'), 'method' => 'post'] )}}
{{Form::hidden("slug_lang", $slug_lang)}}
{{FormField::description(["id" => "slugme", "type" => "text"])}}
{{FormField::slug(array('label'=>"Nome link", "id" => "slug"))}}
<span class="text-danger">{{$errors->first('slug')}}</span>
{{Form::hidden('id')}}
{{Form::submit('Salva', array("class"=>"btn btn-large btn-primary pull-right tab-remember"))}}
{{Form::close()}}

@section('footer_scripts')
@parent
<script>
    $(function(){
        $('#slugme').slugIt();
    });
</script>
{{HTML::script('packages/palmabit/catalog/js/slugit.js')}}
@stop
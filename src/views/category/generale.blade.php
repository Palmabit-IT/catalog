{{-- messaggi vari --}}
<?php $message = Session::get('message'); ?>
@if( isset($message) )
<div class="alert alert-success">{{$message}}</div>
@endif

<h3>Aggiungi nuova categoria</h3>
{{Form::model($categorie, array('url' => array(URL::action('Category\Controllers\CategoryController@postModifica'), $categorie->id), 'method' => 'post') )}}
{{Form::hidden("slug_lingua", $slug_lingua)}}
{{FormField::descrizione(["id" => "slugme"])}}
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
{{HTML::script('admin/js/slugit.js')}}
@stop
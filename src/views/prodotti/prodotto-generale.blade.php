{{-- messaggi vari --}}
<?php $message = Session::get('message'); ?>
@if( isset($message) )
<div class="alert alert-success">{{$message}}</div>
@endif

<h3>Aggiungi nuovo prodotto</h3>
{{Form::model($prodotto, array('url' => array(URL::action('Prodotti\Controllers\ProdottoController@postModifica'), $prodotto->id), 'method' => 'post') )}}
{{Form::hidden("slug_lingua", $slug_lingua)}}
{{FormField::codice()}}
<span class="text-danger">{{$errors->first('codice')}}</span>
{{FormField::nome(["id" => "slugme"])}}
<span class="text-danger">{{$errors->first('nome')}}</span>
{{FormField::slug(["label"=>"Nome link", "id" => "slug"])}}
<span class="text-danger">{{$errors->first('slug')}}</span>
{{FormField::descrizione(["type" => "textarea"])}}
<span class="text-danger">{{$errors->first('descrizione')}}</span>
{{FormField::descrizione_estesa(["label" => "Note"])}}
<span class="text-danger">{{$errors->first('descrizione_estesa')}}</span>
<div class="form-group">
    {{Form::label("in_evidenza","Prodotto in evidenza")}}
    {{Form::select('in_evidenza', ["1" => "Sì", "0" => "No"], (isset($prodotto->in_evidenza) && $prodotto->in_evidenza) ? $prodotto->in_evidenza : "0", ["class"=> "form-control"] )}}
</div>
{{Form::hidden('id')}}
{{Form::submit('Salva', array("class"=>"btn btn-primary pull-right tab-remember margin-bottom-30"))}}
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
{{-- messaggi vari --}}
<?php $message = Session::get('message'); ?>
@if( isset($message) )
<div class="alert alert-success">{{$message}}</div>
@endif

<h3>Aggiungi nuovo prodotto</h3>
{{Form::model($product, array('url' => array(URL::action('Palmabit\Catalog\Controllers\ProductsController@postEdit'), $product->id), 'method' => 'post') )}}
{{Form::hidden("slug_lang", $slug_lang)}}
{{FormField::code(["label" => "Codice:"])}}
<span class="text-danger">{{$errors->first('code')}}</span>
{{FormField::name(["id" => "slugme", "label" => "Nome prodotto:"])}}
<span class="text-danger">{{$errors->first('name')}}</span>
{{FormField::slug(["label"=>"Nome link:", "id" => "slug"])}}
<span class="text-danger">{{$errors->first('slug')}}</span>
{{FormField::description(["type" => "textarea", "label" => "Descrizione:"])}}
<span class="text-danger">{{$errors->first('description')}}</span>
{{FormField::description_long(["label" => "Note", "type" => "textarea"])}}
<span class="text-danger">{{$errors->first('description_long')}}</span>
<div class="form-group">
    {{Form::label("featured","Prodotto in evidenza")}}
    {{Form::select('featured', ["1" => "Sì", "0" => "No"], (isset($product->featured) && $product->featured) ? $product->featured: "0", ["class"=> "form-control"] )}}
</div>
<div class="form-group">
    {{Form::label("public","Prodotto pubblico")}}
    {{Form::select('public', ["1" => "Sì", "0" => "No"], (isset($product->public) && $product->public) ? $product->public: "0", ["class"=> "form-control"] )}}
</div>
<div class="form-group">
    {{Form::label("offer","Prodotto in offerta")}}
    {{Form::select('offer', ["1" => "Sì", "0" => "No"], (isset($product->offer) && $product->offer) ? $product->offer: "0", ["class"=> "form-control"] )}}
</div>
{{Form::hidden('id')}}
{{Form::submit('Salva', array("class"=>"btn btn-primary pull-right tab-remember margin-bottom-30"))}}
{{Form::close()}}

@section('footer_scripts')
@parent
{{HTML::script('packages/palmabit/catalog/js/slugit.js')}}
<script>
    $(function(){
        $('#slugme').slugIt();
    });
</script>
@stop
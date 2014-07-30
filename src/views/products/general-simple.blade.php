{{-- messaggi vari --}}
<?php $message = Session::get('message'); ?>
@if( isset($message) )
<div class="alert alert-success">{{$message}}</div>
@endif
{{-- error messages --}}
@if( $errors->has('duplication') )
<div class="alert alert-danger">{{$errors->first('duplication')}}</div>
@endif

<div class="row">
    <div class="col-md-12">
        <h3>Anagrafica prodotto</h3>
    </div>

    <div class="col-md-6">
        {{Form::model($product, array('url' =>
        array(URL::action('Palmabit\Catalog\Controllers\ProductsController@postEdit'), $product->id), 'method' =>
        'post') )}}
<!--        {{Form::hidden("slug_lang", $slug_lang)}}-->
        {{FormField::code(["label" => "Codice*: ", 'readonly' => 'readonly'])}}
        <span class="text-danger">{{$errors->first('code')}}</span>
        {{FormField::name(["id" => "slugme", "label" => "Nome*: "])}}
        <span class="text-danger">{{$errors->first('name')}}</span>
        {{FormField::slug(["label"=>"Slug*: ", "id" => "slug"])}}
        <span class="text-danger">{{$errors->first('slug')}}</span>
        {{FormField::description(["type" => "textarea", "label" => "Descrizione breve*: ", "rows"=>5])}}
        <span class="text-danger">{{$errors->first('description')}}</span>
        {{FormField::long_description(["label" => "Descrizione lunga", "type" => "textarea"])}}
        <span class="text-danger">{{$errors->first('long_description')}}</span>
    </div>
    <div class="col-md-6">
        {{FormField::video_link(["type" => "hidden", "label" => ""])}}
        {{Form::hidden('price1', null, ['class' => 'form-control'])}}
        {{Form::hidden('price2', null, ['class' => 'form-control'])}}
        {{Form::hidden('price3',null, ['class' => 'form-control'])}}
        {{Form::hidden('price4', null, ['class' => 'form-control'])}}
        {{Form::select('quantity_pricing_enabled', ["1" => "Sì", "0" => "No"],
        (isset($product->quantity_pricing_enabled) && $product->quantity_pricing_enabled) ?
        $product->quantity_pricing_enabled: "0", ["class"=> "hidden"] )}}
        {{FormField::quantity_pricing_quantity(["label" => "","class" => "hidden"])}}
        {{FormField::quantity_pricing_quantity_non_professional(["label" => "","class" => "hidden"])}}
        {{Form::select('with_vat', ["1" => "Sì", "0" => "No"], null, ["class"=> "hidden"] )}}
        {{Form::select('stock', ["1" => "Sì", "0" => "No"], null, ["class"=> "hidden"] )}}
        {{Form::select('professional', ["0" => "No", "1" => "Sì"], null, ["class"=> "hidden"] )}}
        {{Form::select('public', ["1" => "Sì", "0" => "No"], null, ["class"=> "hidden"] )}}
        {{Form::select('featured', ["0" => "No", "1" => "Sì"], null, ["class"=> "hidden"] )}}
        {{Form::select('offer', ["0" => "No", "1" => "Sì"], null, ["class"=> "hidden"] )}}
        {{Form::hidden('id')}}
        {{Form::hidden('form_name','products.general')}}
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12">
        {{Form::submit('Salva', array("class"=>"btn btn-primary tab-remember margin-bottom-30"))}}
        @if($product->exists)
        <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@delete',array('id' => $product->id))}}" class="btn btn-danger cancella-prodotto" style="margin-bottom:30px">Cancella</a>
        @endif
        {{Form::updateOldLanguageInput()}}
        {{Form::close()}}
    </div>
</div>

@section('footer_scripts')
@parent
{{HTML::script('packages/palmabit/catalog/js/slugit.js')}}
<script>
    $(function () {
        $('#slugme').slugIt();
    });
    $(".cancella-prodotto").click(function(){
        return confirm("Sei sicuro di volere eliminare il prodotto corrente?");
    });
</script>
@stop
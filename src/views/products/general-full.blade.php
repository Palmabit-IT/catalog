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
        {{Form::model($product, array('url' => array(URL::action('Palmabit\Catalog\Controllers\ProductsController@postEdit'), $product->id), 'method' => 'post') )}}
        {{Form::hidden("slug_lang", $slug_lang)}}
        {{FormField::code(["label" => "Codice*: "])}}
        <span class="text-danger">{{$errors->first('code')}}</span>
        {{FormField::name(["id" => "slugme", "label" => "Nome* :"])}}
        <span class="text-danger">{{$errors->first('name')}}</span>
        {{FormField::slug(["label"=>"Slug*: ", "id" => "slug"])}}
        <span class="text-danger">{{$errors->first('slug')}}</span>
        {{FormField::description(["type" => "textarea", "label" => "Descrizione breve*: ", "rows"=>5])}}
        <span class="text-danger">{{$errors->first('description')}}</span>
        {{FormField::long_description(["label" => "Descrizione lunga", "type" => "textarea"])}}
        <span class="text-danger">{{$errors->first('long_description')}}</span>
    </div>
    <div class="col-md-6">
        {{FormField::video_link(["type" => "text", "label" => "URL video (youtube):"])}}
        <span class="text-danger">{{$errors->first('video_link')}}</span>
        <div class="form-group">
            <label for=​"price1" class=​"control-label">​Prezzo1*:​</label>​
            <div class="input-group">
              <span class="input-group-addon">€</span>
              {{--  please keep in mind this null will be replaced with the populated value from the form class --}}
              {{Form::text('price1', null, ['class' => 'form-control'])}}
            </div>
        </div>
        <span class="text-danger">{{$errors->first('price1')}}</span>
        <div class="form-group">
            <label for=​"price2" class=​"control-label">​Prezzo2*:</label>​
            <div class="input-group">
              <span class="input-group-addon">€</span>
                {{--  please keep in mind this null will be replaced with the populated value from the form class --}}
                {{Form::text('price2', null, ['class' => 'form-control'])}}
            </div>
        </div>
        <span class="text-danger">{{$errors->first('price2')}}</span>
        <div class="form-group">
            <label for=​"price3" class=​"control-label">​Prezzo3*:*</label>​
            <div class="input-group">
              <span class="input-group-addon">€</span>
                {{--  please keep in mind this null will be replaced with the populated value from the form class --}}
                {{Form::text('price3',null, ['class' => 'form-control'])}}
            </div>
        </div>
        <span class="text-danger">{{$errors->first('price3')}}</span>
        <div class="form-group">
            <label for=​"price4" class=​"control-label">​Prezzo4*:</label>​
            <div class="input-group">
                <span class="input-group-addon">€</span>
                {{--  please keep in mind this null will be replaced with the populated value from the form class --}}
                {{Form::text('price4', null, ['class' => 'form-control'])}}
            </div>
        </div>
        <span class="text-danger">{{$errors->first('price4')}}</span>
        <div class="form-group">
            {{Form::label("","Abilita gestione quantità: ")}}
            {{Form::select('quantity_pricing_enabled', ["1" => "Sì", "0" => "No"], (isset($product->quantity_pricing_enabled) && $product->quantity_pricing_enabled) ? $product->quantity_pricing_enabled: "0", ["class"=> "form-control"] )}}
        </div>
        {{FormField::quantity_pricing_quantity(["label" => "Quantità professionista"])}}
        <span class="text-danger">{{$errors->first('quantity_pricing_quantity')}}</span>
        {{FormField::quantity_pricing_quantity_non_professional(["label" => "Quantità non professionista"])}}
        <span class="text-danger">{{$errors->first('quantity_pricing_quantity_non_professional')}}</span>
        <div class="form-group">
            {{Form::label("with_vat","Iva inclusa: ")}}
            {{Form::select('with_vat', ["1" => "Sì", "0" => "No"], null, ["class"=> "form-control"] )}}
        </div>
        <div class="form-group">
            {{Form::label("stock","Giacenza (disponibilità in magazzino)")}}
            {{Form::select('stock', ["1" => "Sì", "0" => "No"], null, ["class"=> "form-control"] )}}
        </div>
        <div class="form-group">
            {{Form::label("professional","Prodotto professionale")}}
            {{Form::select('professional', ["0" => "No", "1" => "Sì"], null, ["class"=> "form-control"] )}}
        </div>
        <div class="form-group">
            {{Form::label("public","Prodotto pubblico")}}
            {{Form::select('public', ["1" => "Sì", "0" => "No"], null, ["class"=> "form-control"] )}}
        </div>
        <div class="form-group">
            {{Form::label("featured","Prodotto in evidenza")}}
            {{Form::select('featured', ["0" => "No", "1" => "Sì"], null, ["class"=> "form-control"] )}}
        </div>
        <div class="form-group">
            {{Form::label("offer","Prodotto in offerta")}}
            {{Form::select('offer', ["0" => "No", "1" => "Sì"], null, ["class"=> "form-control"] )}}
        </div>
        {{Form::hidden('id')}}
        {{Form::hidden('form_name','products.general')}}
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12">
        {{Form::submit('Salva', array("class"=>"btn btn-primary tab-remember margin-bottom-30"))}}
        @if($product->exists)
            <a href="{{URL::route('products.delete.bysluglang',array('slug_lang' => $product->slug_lang))}}" class="btn btn-danger cancella-prodotto" style="margin-bottom:30px">Cancella</a>
        @endif
        {{Form::updateOldLanguageInput()}}
        {{Form::close()}}
    </div>
</div>

@section('footer_scripts')
@parent
{{HTML::script('packages/palmabit/catalog/js/slugit.js')}}
<script>
    $(function(){
        $('#slugme').slugIt();
    });
    $(".cancella-prodotto").click(function(){
        return confirm("Sei sicuro di volere eliminare il prodotto corrente in tutte le lingue?");
    });
</script>
@stop
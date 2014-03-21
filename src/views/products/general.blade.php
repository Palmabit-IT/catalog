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
        {{FormField::code(["label" => "Codice"])}}
        <span class="text-danger">{{$errors->first('code')}}</span>
        {{FormField::name(["id" => "slugme", "label" => "Nome"])}}
        <span class="text-danger">{{$errors->first('name')}}</span>
        {{FormField::slug(["label"=>"Slug", "id" => "slug"])}}
        <span class="text-danger">{{$errors->first('slug')}}</span>
        {{FormField::description(["type" => "textarea", "label" => "Descrizione breve", "rows"=>5])}}
        <span class="text-danger">{{$errors->first('description')}}</span>
        {{FormField::long_description(["label" => "Descrizione lunga", "type" => "textarea"])}}
        <span class="text-danger">{{$errors->first('long_description')}}</span>
    </div>
    <div class="col-md-6">
        {{FormField::video_link(["type" => "text", "label" => "URL video (youtube):"])}}
        <span class="text-danger">{{$errors->first('video_link')}}</span>
        <div class="form-group">
            <label for=​"price1" class=​"control-label">​Prezzo1: ​</label>​
            <div class="input-group">
              <span class="input-group-addon">€</span>
              <input type="text" name="price1" value="{{(isset($product->price1) && $product->price1) ? $product->price1 : '0.00' }}" class="form-control">
            </div>
        </div>
        <span class="text-danger">{{$errors->first('price1')}}</span>
        <div class="form-group">
            <label for=​"price2" class=​"control-label">​Prezzo2: ​</label>​
            <div class="input-group">
              <span class="input-group-addon">€</span>
              <input type="text" name="price2" value="{{(isset($product->price2) && $product->price2) ? $product->price2 : '0.00' }}" class="form-control">
            </div>
        </div>
        <span class="text-danger">{{$errors->first('price2')}}</span>
        <div class="form-group">
            <label for=​"price3" class=​"control-label">​Prezzo3: ​</label>​
            <div class="input-group">
              <span class="input-group-addon">€</span>
              <input type="text" name="price3" value="{{(isset($product->price3) && $product->price3) ? $product->price3 : '0.00' }}" class="form-control">
            </div>
        </div>
        <span class="text-danger">{{$errors->first('price4')}}</span>
        <div class="form-group">
            <label for=​"price4" class=​"control-label">​Prezzo4: ​</label>​
            <div class="input-group">
                <span class="input-group-addon">€</span>
                <input type="text" name="price4" value="{{(isset($product->price4) && $product->price4) ? $product->price4 : '0.00' }}" class="form-control">
            </div>
        </div>
        <span class="text-danger">{{$errors->first('price4')}}</span>
        <div class="form-group">
            {{Form::label("","Abilita gestione quantità: ")}}
            {{Form::select('quantity_pricing_enabled', ["1" => "Sì", "0" => "No"], (isset($product->quantity_pricing_enabled) && $product->quantity_pricing_enabled) ? $product->quantity_pricing_enabled: "0", ["class"=> "form-control"] )}}
        </div>
        {{FormField::quantity_pricing_quantity(["label" => "Quantità"])}}
        <span class="text-danger">{{$errors->first('quantity_pricing_quantity')}}</span>
        <div class="form-group">
            {{Form::label("with_vat","Iva inclusa: ")}}
            {{Form::select('with_vat', ["1" => "Sì", "0" => "No"], (isset($product->with_vat) && $product->with_vat) ? $product->with_vat: "0", ["class"=> "form-control"] )}}
        </div>
        <div class="form-group">
            {{Form::label("stock","Giacenza (disponibilità in magazzino)")}}
            {{Form::select('stock', ["1" => "Sì", "0" => "No"], (isset($product->stock) && $product->stock) ? $product->stock: "0", ["class"=> "form-control"] )}}
        </div>
        <div class="form-group">
            {{Form::label("professional","Prodotto professionale")}}
            {{Form::select('professional', ["1" => "Sì", "0" => "No"], (isset($product->professional) && $product->professional) ? $product->professional: "0", ["class"=> "form-control"] )}}
        </div>
        <div class="form-group">
            {{Form::label("public","Prodotto pubblico")}}
            {{Form::select('public', ["1" => "Sì", "0" => "No"], (isset($product->public) && $product->public) ? $product->public: "0", ["class"=> "form-control"] )}}
        </div>
        <div class="form-group">
            {{Form::label("featured","Prodotto in evidenza")}}
            {{Form::select('featured', ["1" => "Sì", "0" => "No"], (isset($product->featured) && $product->featured) ? $product->featured: "0", ["class"=> "form-control"] )}}
        </div>
        <div class="form-group">
            {{Form::label("offer","Prodotto in offerta")}}
            {{Form::select('offer', ["1" => "Sì", "0" => "No"], (isset($product->offer) && $product->offer) ? $product->offer: "0", ["class"=> "form-control"] )}}
        </div>
        {{Form::hidden('id')}}
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12">
        {{Form::submit('Salva', array("class"=>"btn btn-primary tab-remember margin-bottom-30"))}}
        <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@duplicate', ['id' => $product->id, 'slug_lang' => $slug_lang, '_token' => csrf_token()])}}" class="btn btn-info margin-bottom-30" {{! empty($product->slug) ?: 'disabled="disabled"'}}>Duplica</a>
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
</script>
@stop
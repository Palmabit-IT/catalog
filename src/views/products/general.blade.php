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
            <label for=​"price1" class=​"control-label">​Prezzo: ​</label>​
            <div class="input-group">
              <span class="input-group-addon">€</span>
              {{--  please keep in mind this null will be replaced with the populated value from the form class --}}
              {{Form::text('price', null, ['class' => 'form-control'])}}
            </div>
            <span class="text-danger">{{$errors->first('price')}}</span>
        </div>
        <div class="form-group">
            {{Form::label("stock","Giacenza (disponibilità in magazzino)")}}
            {{Form::select('stock', ["1" => "Sì", "0" => "No"], null, ["class"=> "form-control"] )}}
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
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12">
        {{Form::submit('Salva', array("class"=>"btn btn-primary tab-remember margin-bottom-30"))}}
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
</script>
@stop
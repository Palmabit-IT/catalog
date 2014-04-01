@extends('catalog::layouts.base-2-cols-multilanguage')

@section('title')
{{$app_name}} Admin area: prodotti
@stop

@section('content')
    {{-- Lista dei prodotti --}}
    <h3>Catalogo prodotti</h3>
    <div class="col-md-12">
        <div class="col-md-8">
            {{-- messaggi vari --}}
            <?php $message = Session::get('message'); ?>
            @if( isset($message) )
            <div class="alert alert-success">{{$message}}</div>
            @endif
            @if($errors && ! $errors->isEmpty() )
            @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{$error}}</div>
            @endforeach
            @endif

            {{-- Lista prodotti --}}
            <ul class="list-group">
            @if(! $products->isEmpty() )
            @foreach($products as $product)
                <li class="list-group-item">
                    {{$product->name}} - {{$product->description}}
                    <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@duplicate', ['id' => $product->id, 'slug_lang' => $product->slug_lang, '_token' => csrf_token()])}}" class="pull-right"><i class="glyphicon glyphicon-plus"></i> duplica</a>
                    <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@delete',array('id' => $product->id) )}}" class="pull-right cancella" style="margin-right:10px"><i class="glyphicon glyphicon-trash"></i> cancella</a>
                    <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@getEdit',array('slug_lang'=> $product->slug_lang) )}}" class="pull-right"><i class="glyphicon glyphicon-edit"></i> modifica</a>
                    <span class="pull-right margin-right-30">
                        {{Form::open(["action" => "Palmabit\Catalog\Controllers\ProductsController@postChangeOrder", "class" => "form-inline"])}}
                        {{Form::label('order','Ordine')}}
                        {{Form::select('order', get_select_order_arr(), $product->order, ["class" => "form-control swap-ordine", "style" => "height:20px", "onchange" => "this.form.submit()" ] ) }}
                        {{Form::hidden('id', $product->id)}}
                        {{Form::close()}}
                    </span>
                    <span class="clearfix"></span>
                </li>
            @endforeach
            @else
            <h5>Non ho trovato risultati.</h5>
            @endif
            </ul>
            {{-- Aggiunta nuovo prodotto --}}
            <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@getEdit')}}" class="btn btn-primary pull-right"><i class="glyphicon glyphicon-plus"></i> Aggiungi</a>

            <div style="text-align: center">
                {{ isset($products) ? $products->appends(Input::except(['page']) )->links() : ''}}
            </div>
        </div>
        <div class="col-md-4">
            @include('catalog::products.search')
        </div>
    </div>
@stop

@section('footer_scripts')
@parent
<script>
        $(".cancella").click(function(){
            return confirm("Sei sicuro di volere eliminare il prodotto selezionato?");
        });
</script>
@stop
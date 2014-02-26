@extends('catalog::layouts.base-2-cols-multilanguage')

@section('title')
{{$app_name}} Admin area: prodotti
@stop

@section('content')
    {{-- Lista dei prodotti --}}
    <h3>Lista prodotti</h3>
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
    @if(! empty($products))
    @foreach($products as $product)
        <li class="list-group-item">
            {{$product->name}}
            <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@delete',array('id' => $product->id) )}}"><span class="glyphicon glyphicon-trash pull-right cancella">cancella</span></a>
            <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@getEdit',array('slug_lang'=> $product->slug_lang) )}}"><span class="glyphicon glyphicon-edit pull-right">modifica</span></a>
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
    <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@getEdit')}}" class="btn btn-primary pull-right">Aggiungi</a>

    <div style="text-align: center">
        {{ isset($products) ? $products->links() : ''}}
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
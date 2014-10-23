@extends('catalog::layouts.base-2-cols-multilanguage')

@section('title')
{{$app_name}} Admin area: prodotti
@stop

@section('content')
<div class="row" style="margin-bottom: 20px;">
        <div class="col-md-9">
            {{-- Lista dei prodotti --}}
            <h3>Catalogo prodotti</h3>

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
            <table class="table table-striped">
                <tr>
                    <th>Codice</th>
                    <th>Nome</th>
                    <th>Ordine</th>
                    <th></th>
                    <th></th>
                </tr>
                @if(! $products->isEmpty() )
                @foreach($products as $product)
                    <tr>
                        <td>
                            {{$product->code}}
                        </td>
                        <td>
                            {{$product->name}}
                            <div class="product-flags">
                                <?= \App::make('product_repository')->find($product->id)->presenter()->availableflags; ?>
                            </div>
                        </td>
                        <td>
                            {{Form::open(["action" => "Palmabit\Catalog\Controllers\ProductsController@postChangeOrder", "class" => "form-inline"])}}
                            {{Form::select('order', get_select_order_arr(), $product->order, ["class" => "form-control swap-ordine", "style" => "height:20px", "onchange" => "this.form.submit()" ] ) }}
                            {{Form::hidden('id', $product->id)}}
                            {{Form::close()}}
                        </td>
                        <td>
                            <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@duplicate', ['id' => $product->id, '_token' => csrf_token()])}}" class=""><i class="glyphicon glyphicon-link"></i> duplica</a>
                        </td>
                        <td>
                            <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@getEdit',array('id'=> $product->id) )}}" class=""><i class="glyphicon glyphicon-edit"></i></a>
                            <a href="{{URL::route('products.delete',array('id' => $product->id) )}}" class="cancella" style="margin-right:10px"><i class="glyphicon glyphicon-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
                @else
                <h5>Non ho trovato risultati.</h5>
                @endif
            </table>
            {{-- Aggiunta nuovo prodotto --}}
            <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@getEdit')}}" class="btn btn-primary "><i class="glyphicon glyphicon-plus"></i> Aggiungi</a>
            <div style="text-align: center">
                {{ isset($products) ? $products->appends(Input::except(['page']) )->links() : ''}}
            </div>
        </div>
        <div class="col-md-3">
            @include('catalog::products.search')
        </div>
    <!-- </div> -->
</div>
@stop

@section('footer_scripts')
@parent
<script>
        $(".cancella").click(function(){
            return confirm("Are you sure to delete this product in all the languages?");
        });
</script>
@stop
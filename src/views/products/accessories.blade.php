<div class="row">
    <div class="col-md-12">

        <h3>Accessori</h3>
        <hr/>
        {{-- accessories messages --}}
        <?php $message = Session::get('message_accessories'); ?>
        @if( isset($message) )
        <div class="alert alert-success">{{$message}}</div>
        @endif
        <ul class="list-group">
            @if($presenter->accessories)
            @foreach($presenter->accessories as $_product)
            <li class="list-group-item">
                <span class="glyphicon glyphicon-cog"> {{$_product->decorateLanguage()->name}}</span>
                <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@postDetachProduct', ['_token' => csrf_token(), 'first_product_id' => $product->id, 'second_product_id' => $_product->id] ) }}" class="pull-right delete-acc tab-remember"><i class="glyphicon glyphicon-trash"></i> Rimuovi</a>
            </li>
            @endforeach
            @else
            <h5>Non ci sono accessori associati al prodotto.</h5>
            @endif
        </ul>

        {{-- associate a product --}}
        <h3>Associa un'accessorio</h3>
        {{ Form::open(['action' => 'Palmabit\Catalog\Controllers\ProductsController@postAttachProduct', 'method' => 'post']) }}
        <div class="form-group">
            {{Form::label("product_id","Associa un prodotto")}}
            {{Form::select("second_product_id", get_product_select_arr(), '', ["class" => "form-control"])}}
            {{Form::hidden("first_product_id", $product->id)}}
        </div>
        <hr>
        {{ Form::submit("Associa", ["class" => "btn btn-primary tab-remember"]) }}
        {{ Form::close() }}
    </div>
</div>

@section('footer_scripts')
@parent
<script>
    $(".delete-acc").click(function(){
        return confirm("Sei sicuro di volere rimuovere l'accessorio?");
    });
</script>
@stop
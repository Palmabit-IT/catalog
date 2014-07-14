<div class="row">
    <div class="col-md-12">

    <h3>Associazione categorie</h3>

    {{-- category messages --}}
    <?php $message = Session::get('message_cat'); ?>
    @if( isset($message) )
        <div class="alert alert-success">{{$message}}</div>
    @endif

    @if($errors && ! $errors->isEmpty() )
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{$error}}</div>
        @endforeach
    @endif
    {{-- list of associated categories --}}
    <h5>Categorie già associate</h5>
    <ul class="list-group">
        @if($presenter->categories())
        @foreach($presenter->categories() as $category)
        <li class="list-group-item">
            {{$category->name}}
            <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@postDetachCategory', ['_token' => csrf_token(), 'product_id' => $product->id, 'slug_lang' => $slug_lang, 'category_id' => $category->id])}}" class="pull-right tab-remember cancella"><i class="glyphicon glyphicon-trash"></i> Rimuovi</a>
        </li>
        @endforeach
        @else
            <h5>Non ci sono categorie associate.</h5>
        @endif
    </ul>

    {{-- associate a category --}}
    {{ Form::open(['action' => 'Palmabit\Catalog\Controllers\ProductsController@postAttachCategory', 'method' => 'post']) }}
    <div class="form-group">
        {{Form::label("categoria","Associa al prodotto una nuovo categoria")}}
        {{Form::select("category_id", get_cat_select_arr(), '', ["class" => "form-control"]) }}
        {{Form::hidden("slug_lang", $slug_lang)}}
        {{Form::hidden("product_id", $product->id)}}
    </div>
    <hr>
    {{ Form::submit("Associa", ["class" => "btn btn-primary tab-remember"]) }}
    {{ Form::close() }}

    </div>

</div>

@section('footer_scripts')
@parent
<script>
    $(".cancella").click(function(){
        return confirm("Sei sicuro di volere rimuovere la categoria?");
    });
</script>
@stop
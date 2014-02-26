<h3>Associa una categoria</h3>
{{-- messaggi categoria --}}
<?php $message = Session::get('message_cat'); ?>
@if( isset($message) )
<div class="alert alert-success">{{$message}}</div>
@endif

@if($errors && ! $errors->isEmpty() )
    @foreach($errors->all() as $error)
        <div class="alert alert-danger">{{$error}}</div>
    @endforeach
@endif

{{ Form::open(['action' => 'Palmabit\Catalog\Controllers\ProductsController@postCategory', 'method' => 'post']) }}
<div class="form-group">
    {{Form::label("categoria","Categoria associata")}}
    {{Form::select("category_id", get_cat_select_arr(), $presenter->categories_ids, ["class" => "form-control"]) }}
    {{Form::hidden("slug_lang", $slug_lang)}}
    {{Form::hidden("product_id", $product->id)}}
</div>
{{ Form::submit("Salva", ["class" => "btn btn-primary pull-right tab-remember"]) }}
{{ Form::close() }}
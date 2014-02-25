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

{{ Form::open(['action' => 'Prodotti\Controllers\ProdottoController@postCategoria', 'method' => 'post']) }}
<div class="form-group">
    {{Form::label("categoria","Categoria associata")}}
    {{Form::select("categoria", get_cat_select_arr(), $presenter->categoria_id, ["class" => "form-control"]) }}
    {{Form::hidden("slug_lingua", $slug_lingua)}}
    {{Form::hidden("prodotto_id", $prodotto->id)}}
</div>
{{ Form::submit("Salva", ["class" => "btn btn-primary pull-right tab-remember"]) }}
{{ Form::close() }}
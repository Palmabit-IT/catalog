@extends('catalog::layouts.base-2-cols-multilanguage')

@section('title')
{{$app_name}} Admin area: categorie
@stop
@section('content')
<div class="row" style="margin-bottom: 20px;">
    <div class="col-md-12">
        <h3>Lista categorie</h3>
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
        {{-- Lists categories --}}
        <table class="table table-striped">
            <tr>
                <th>Nome</th>
                <th>Ordine</th>
                <th>Categoria padre</th>
                <th></th>
            </tr>
        @if(! empty($categories))
        @foreach($categories as $category)
            <tr>
                <td>{{$category->description}}</td>
                <td>
                    {{Form::open(["action" => "Palmabit\Catalog\Controllers\CategoryController@postChangeOrder", "class" => "form-inline"])}}
                    {{Form::select('order', get_select_order_arr(), $category->order, ["class" => "form-control swap-ordine", "style" => "height:20px", "onchange" => "this.form.submit()" ] ) }}
                    {{Form::hidden('id', $category->id)}}
                    {{Form::close()}}
                </td>
                <td>
                    {{Form::open(['action' => 'Palmabit\Catalog\Controllers\CategoryController@postSetParentList', 'method' => 'post', 'id' => 'form-select-cat','class' => 'form-inline'])}}
                    <div class="form-group">
                        {{Form::select("parent_id", get_cat_select_arr(true), $category->parent_id, ["class" => "form-control", "style" => "height:20px", "onchange" => "this.form.submit()"]) }}
                    </div>
                    {{Form::hidden("id", $category->id)}}
                    {{Form::close()}}
                </td>
                <td>
                    <a href="{{URL::action('Palmabit\Catalog\Controllers\CategoryController@getEdit',array('slug_lang'=> $category->slug_lang) )}}"><i class="glyphicon glyphicon-edit"></i></a>
                    <a href="{{URL::action('Palmabit\Catalog\Controllers\CategoryController@delete',array('id' => $category->id) )}}" class="delete"><i class="glyphicon glyphicon-trash"></i></a>
                </td>
            </tr>
        @endforeach
        @else
            <h5>Non ho trovato risultati.</h5>
        @endif
        </table>
        {{-- Add new category --}}
        <a href="{{URL::action('Palmabit\Catalog\Controllers\CategoryController@getEdit')}}" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> Aggiungi nuova</a>
    </div>
</div>
@stop

@section('footer_scripts')
@parent
<script>
    $(".delete").click(function(){
        return confirm("Sei sicuro di volere eliminare la categoria selezionata?");
    });
    $("#form-select-lang").hide();
</script>
@stop
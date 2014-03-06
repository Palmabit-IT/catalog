@extends('catalog::layouts.base-2-cols-multilanguage')

@section('title')
{{$app_name}} Admin area: categorie
@stop
@section('content')
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
    <ul class="list-group">
    @if(! empty($categories))
    @foreach($categories as $category)
        <li class="list-group-item">
            {{$category->description}}
            <a href="{{URL::action('Palmabit\Catalog\Controllers\CategoryController@delete',array('id' => $category->id) )}}"><span class="glyphicon glyphicon-trash pull-right cancella">cancella</span></a>
            <a href="{{URL::action('Palmabit\Catalog\Controllers\CategoryController@getEdit',array('slug_lang'=> $category->slug_lang) )}}"><span class="glyphicon glyphicon-edit pull-right">modifica</span></a>
            <span class="pull-right margin-right-30">
            {{Form::open(['action' => 'Palmabit\Catalog\Controllers\CategoryController@postSetParentList', 'method' => 'post', 'id' => 'form-select-cat','class' => 'form-inline'])}}
            <div class="form-group">
                {{Form::label("categoria","Padre:")}}
                {{Form::select("parent_id", get_cat_select_arr(true), $category->parent_id, ["class" => "form-control", "style" => "height:20px", "onchange" => "this.form.submit()"]) }}
            </div>
            {{Form::hidden("id", $category->id)}}
            {{Form::close()}}
            </span>
            <span class="clearfix"></span>
        </li>
    @endforeach
    @else
        <h5>Non ho trovato risultati.</h5>
    @endif
    </ul>
    {{-- Add new category --}}
    <a href="{{URL::action('Palmabit\Catalog\Controllers\CategoryController@getEdit')}}" class="btn btn-primary pull-right">Aggiungi</a>
@stop

@section('footer_scripts')
@parent
<script>
    $(".cancella").click(function(){
        return confirm("Sei sicuro di volere eliminare la categoria selezionata?");
    });
</script>
@stop
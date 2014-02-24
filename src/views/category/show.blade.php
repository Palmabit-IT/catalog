@extends('admin.layouts.base-2cols')

@section('title')
{{$nome_sito}} Admin area: categorie
@stop

@section('content')
    {{-- Lista delle categorie --}}
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
    {{-- Lista categorie --}}
    <ul class="list-group">
    @if(! empty($categorie))
    @foreach($categorie as $categoria)
        <li class="list-group-item">
            <a href="{{URL::action('Category\Controllers\CategoryController@getModifica',array('slug_lingua'=> $categoria->slug_lingua) )}}">{{$categoria->descrizione}}</a>
            <a href="{{URL::action('Category\Controllers\CategoryController@cancella',array('id' => $categoria->id) )}}"><span class="glyphicon glyphicon-trash pull-right cancella"> cancella</span></a>
        </li>
    @endforeach
    @else
        <h5>Non ho trovato risultati.</h5>
    @endif
    </ul>
    {{-- Aggiunta nuova categoria --}}
    <a href="{{URL::action('Category\Controllers\CategoryController@getModifica')}}" class="btn btn-primary pull-right">Aggiungi</a>
@stop

@section('footer_scripts')
@parent
<script>
    $(".cancella").click(function(){
        return confirm("Sei sicuro di volere eliminare la categoria selezionata?");
    });
</script>
@stop
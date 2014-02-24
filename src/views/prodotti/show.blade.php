@extends('admin.layouts.base-2cols')

@section('title')
{{$nome_sito}} Admin area: prodotti
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
    @if(! empty($prodotti))
    @foreach($prodotti as $prodotto)
        <li class="list-group-item">
            <a href="{{URL::action('Prodotti\Controllers\ProdottoController@getModifica',array('slug_lingua'=> $prodotto->slug_lingua) )}}" class="pull-left">{{$prodotto->nome}}</a>
            <a href="{{URL::action('Prodotti\Controllers\ProdottoController@cancella',array('id' => $prodotto->id) )}}"><span class="glyphicon glyphicon-trash pull-right cancella"> cancella</span></a>
            <span class="pull-right margin-right-30">
                {{Form::open(["action" => "Prodotti\Controllers\ProdottoController@postModificaOrdine", "class" => "form-inline"])}}
                {{Form::label('ordine','Ordine')}}
                {{Form::select('ordine', get_select_ordine_arr(), $prodotto->ordine, ["class" => "form-control swap-ordine", "style" => "height:20px", "onchange" => "this.form.submit()" ] ) }}
                {{Form::hidden('id', $prodotto->id)}}
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
    <a href="{{URL::action('Prodotti\Controllers\ProdottoController@getModifica')}}" class="btn btn-primary pull-right">Aggiungi</a>

    <div style="text-align: center">
        {{ isset($prodotti) ? $prodotti->links() : ''}}
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
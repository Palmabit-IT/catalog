@extends('catalog::layouts.base-2-cols-multilanguage')

@section('title')
{{$app_name}} Admin area: ordine
@stop
@section('content')
<h3><i class="glyphicon glyphicon-briefcase
"></i> Dettaglio ordine</h3>
@if($errors && ! $errors->isEmpty() )
    <div class="alert alert-danger">Errore nella visualizzazione del dettaglio ordine.</div>
@else
    <p><b>Utente:</b> {{$order_presenter->author_email}} <b>Data ordine:</b> {{$order_presenter->date}} <b>Importo totale: </b>{{$order_presenter->total_price}} €</p>
    <h3>Righe ordine:</h3>
    <ul class="list-group">
    @foreach($order->row_orders()->get() as $row)
        <li class="list-group-item"><a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@getEdit',['slug_lang' => $row->getProductPresenter()->slug_lang])}}">{{$row->getProductPresenter()->name}}</a> quantità: {{$row->quantity}} prezzo_totale: {{$row->total_price}} </li>
    @endforeach
    </ul>
@endif
@stop
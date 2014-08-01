@extends('catalog::layouts.base-2-cols-multilanguage')

@section('title')
{{$app_name}} Admin area: ordine
@stop
@section('content')
<h3><i class="glyphicon glyphicon-briefcase
"></i> Dettaglio ordine #{{$order_presenter->id}}</h3>
@if($errors && ! $errors->isEmpty() )
    <div class="alert alert-danger">Errore nella visualizzazione del dettaglio ordine.</div>
@else
	<br>
    <table class="table table-striped">
    	<tr>
    		<td>Utente</td><td><b><a href="{{URL::action('Palmabit\Authentication\Controllers\UserController@editUser',['id' => $order_presenter->author->id])}}">{{$order_presenter->author_email}}</a></b></td>
    	</tr>
    	<tr>
	    	<td>Data ordine</td><td><b>{{$order_presenter->date}}</b></td>
    	</tr>
    	<tr>
	    	<td>Importo totale</td><td><b>{{$order_presenter->total_price}} €</b></td>
    	</tr>
    </table>
    <br>
    <h4>Righe ordine</h4>
	<table class="table table-striped">
        <tr>
            <th>Nome</th>
            <th>Codine</th>
            <th>Quantit&agrave;</th>
            <th>Prezzo totale</th>
        </tr>
	    @foreach($order->row_orders()->get() as $row)
    	<tr>
    		<td>
	        	<a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@getEdit',['id' => $row->product_id])}}">{{$row->getProductPresenter()->name}}</a>
    		</td>
    		<td>
    			{{$row->getProductPresenter()->name}}
    		</td>
    		<td>
    			{{$row->quantity}}
    		</td>
    		<td>
    			{{$row->total_price}}
    		</td>
    	</tr>
    	@endforeach
    </table>
@endif
@stop
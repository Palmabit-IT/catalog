@extends('catalog::layouts.base-2-cols-multilanguage')
<?php use Jacopo\Bootstrap3Table\BootstrapTable; ?>
    @section('title')
{{$app_name}} Admin area: ordine
@stop
@section('content')
<h3>Lista ordini</h3>
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
{{-- Lists orders --}}
<ul class="list-group">
@if(! empty($orders))
<?php

    $table = new BootstrapTable();
    $table->setConfig(["table-striped" => true]);
    $table->setHeader(["Utente","Data ordine", "Importo totale",""] );

    foreach($orders as $order)
    {
        $table->addRows([
                        $order->author_email,
                        $order->date,
                        $order->total_price,
                        link_to_action('Palmabit\Catalog\Controllers\OrderController@show', 'dettaglio', ['id' => $order->id])
                        ]);
    }

    echo $table;

    echo $orders->getLinks();
?>
    @else
    <h5>Non ho trovato ordini.</h5>
    @endif
</ul>
@stop
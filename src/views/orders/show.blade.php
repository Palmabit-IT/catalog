@extends('catalog::layouts.base-2-cols-multilanguage')

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
    @foreach($orders as $order)
    <li class="list-group-item">
        {{$order->author_email}}
        {{$order->date}}
        {{$order->total_price}} €
        <a href="{{URL::action('Palmabit\Catalog\Controllers\OrderController@show',['id' => $order->id])}}" class="pull-right"><i class="glyphicon glyphicon-briefcase"></i> dettaglio</a>
            <span class="pull-right margin-right-30">
        <span class="clearfix"></span>
    </li>
    @endforeach
    @else
    <h5>Non ho trovato ordini.</h5>
    @endif
</ul>
@stop
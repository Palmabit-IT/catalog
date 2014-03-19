<!DOCTYPE html>
<?php use Palmbit\Catalog\Models\Product; ?>
<?php $profile_info = Session::get('profile_info'); ?>
<html lang="it">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>{{Config::get('authentication::app_name')}}: {{L::t('Order submitted successfully')}}</h2>
<div>
    {{L::t('Thanks')}} {{ $body['email'] }}
    <strong>{{L::t('Order number')}}: {{$body['order']->id}} {{L::t('submitted successfully')}}</strong>
    <br/>
    <strong>Dettagli ordine:</strong>
    <ul>
        @foreach($body['order']->row_orders()->get() as $order)
            <? $product = Product::find($order->product_id); ?>
            <li>
                <strong>{{L::t('Name')}}: </strong>{{$product->name}}
            </li>
        @endforeach
    </ul>
    {{-- i dettagli della spedizione sono in profile_info e sono uguali all'input del form dove si può modificare indirizzo spedizione e billing --}}
    <a href="{{URL::to('/')}}" target="_blank">{{L::t('View your profile for details')}}</a>
</div>
</body>
</html>
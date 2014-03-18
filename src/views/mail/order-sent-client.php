<!DOCTYPE html>
<?php use Palmbit\Catalog\Models\Product; ?>
<html lang="it">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>{{Config::get('authentication::app_name')}}: {{L::t('Order submitted successfully')}}</h2>
<div>
    Buongiorno {{ $body['email'] }}
    <strong>{{L::t('Order number')}}: {{$body['order']->id}} {{L::t('submitted successfully')}}.</strong>
    <br/>
    <strong>{{L::t('Order details')}}:</strong>
    <ul>
        @foreach($body['order']->row_order()->get() as $order)
            <? $product = Product::find($order->product_id); ?>
            <li>
                <strong>{{L::t('Name')}}: </strong>{{$product->name}}
            </li>
        @endforeach
    </ul>
    <a href="{{URL::to('/')}}" target="_blank">Homepage {{Config::get('authentication::app_name')}}</a>
</div>
</body>
</html>
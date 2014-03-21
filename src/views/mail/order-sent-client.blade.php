<!DOCTYPE html>
<?php use Palmabit\Catalog\Models\Product; ?>
<html lang="it">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>{{Config::get('authentication::app_name')}}: {{L::t('Order submitted successfully')}}</h2>
<div>
    {{L::t('Hello')}} {{ $body['email'] }}
    <strong>{{L::t('Order number')}}: {{$body['order']->id}} {{L::t('submitted successfully')}}.</strong>
    <br/>
    <strong>{{L::t('Order details')}}:</strong>
    @foreach($body['order']->row_orders()->get() as $order)
        <ul>
            <?php $product = Product::find($order->product_id); ?>
            <li>
                <strong>{{L::t('Name')}}: </strong>{{$product->name}}
            </li>
            <li>
                <strong>{{L::t('Code')}}: </strong>{{$product->code}}
            </li>
            <li>
                <strong>{{L::t('Quantity')}}: </strong>{{$order->quantity}}
            </li>
            <li>
                <strong>{{L::t('Price')}}: </strong>{{$order->total_price}}
            </li>
        </ul>
    @endforeach
    <hr>
    <h3>{{L::t('Billing')}}</h3>
    <ul>
        <li>{{L::t('Billing address')}}: {{$profile_info->billing_address}}, {{$profile_info->billing_address_zip}} {{$profile_info->billing_city}} {{$profile_info->billing_state}} {{$profile_info->billing_country}}</li>
        <li>{{L::t('VAT')}}: {{$profile_info->vat}}</li>
    </ul>
    <hr>
    <h3>{{L::t('Shipping')}}</h3>
    <ul>
        <li>{{L::t('Shipping address')}}: {{$profile_info->shipping_address}}, {{$profile_info->shipping_address_zip}} {{$profile_info->shipping_city}} {{$profile_info->shipping_state}} {{$profile_info->shipping_country}}</li>
    </ul>
    <br>
    <a href="{{URL::to('/')}}" target="_blank">Homepage {{Config::get('authentication::app_name')}}</a>
</div>
</body>
</html>
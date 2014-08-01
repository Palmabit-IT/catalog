<!DOCTYPE html>
<?php use Palmabit\Catalog\Models\Product; ?>
<?php $profile_info = (object)Session::get('profile_info'); ?>
<?php $body['order'] = Session::get($body['session_order_key']); ?>
<html lang="it">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>{{Config::get('authentication::app_name')}}: {{L::t('Order submitted successfully')}}</h2>
<div>
    {{L::t('Hello')}} {{ $body['email'] }},<br/>
    <strong>{{L::t('Order number')}}: {{$body['order']->id}} {{L::t('submitted successfully')}}.</strong>
    <br/>
    <strong>{{L::t('Order details')}}:</strong>
    @foreach($body['order']->getRowOrders() as $order)
            <?php $product = Product::find($order->product_id); ?>
        <ul>
            <li>
                <strong>{{L::t('Name')}}: </strong>{{$product->decorateLanguage()->name}}
            </li>
            <li>
                <strong>{{L::t('Code')}}: </strong>{{$product->code}}
            </li>
            <li>
                <strong>{{L::t('Quantity')}}: </strong>{{$order->quantity}}
            </li>
            <li>
                <strong>{{L::t('Unitary price')}} </strong>{{round($order->total_price / $order->quantity, 2)}}
            </li>
            <li>
                <strong>{{L::t('Total Price')}}: </strong>{{$order->total_price}}
            </li>
        </ul>
    @endforeach
    <hr>
    <h5>{{L::t('Billing')}}</h5>
    <ul>
        <li>
            <strong>{{L::t('First name')}}: </strong> {{isset($profile_info->billing_first_name) ? $profile_info->billing_first_name : 'Non disponibile'}}
        </li>
        <li>
            <strong>{{L::t('Last name')}}: </strong> {{isset($profile_info->billing_last_name) ? $profile_info->billing_last_name : 'Non disponibile'}}
        </li>
        <li>
            <strong>{{L::t('Company')}}: </strong> {{isset($profile_info->billing_company) ? $profile_info->billing_company : 'Non disponibile'}}
        </li>
        <li>
            <strong>{{L::t('VAT')}}: </strong> {{isset($profile_info->vat) ? $profile_info->vat : 'Non disponibile'}}
        </li>
        <li>
            <strong>{{L::t('Billing address')}}: </strong> {{isset($profile_info->billing_address) ? $profile_info->billing_address : 'Non disponibile'}}
            , {{isset($profile_info->billing_city) ? $profile_info->billing_city : 'Non disponibile'}}
            , {{isset($profile_info->billing_address_zip) ? $profile_info->billing_address_zip : 'Non disponibile'}}
            , {{isset($profile_info->billing_country) ? $profile_info->billing_country : 'Non disponibile'}}
            , {{isset($profile_info->billing_state) ? $profile_info->billing_state : 'Non disponibile'}}
        </li>
    </ul>
    <h5>{{L::t('Shipping')}}</h5>
    <ul>
        <li>
            <strong>{{L::t('First name')}}: </strong> {{isset($profile_info->shipping_first_name) ? $profile_info->shipping_first_name : 'Non disponibile'}}
        </li>
        <li>
            <strong>{{L::t('Last name')}}: </strong> {{isset($profile_info->shipping_last_name) ? $profile_info->shipping_last_name : 'Non disponibile'}}
        </li>
        <li>
            <strong>{{L::t('Company')}}: </strong> {{isset($profile_info->shipping_company) ? $profile_info->shipping_company : 'Non disponibile'}}
        </li>
        <li>
            <strong>{{L::t('VAT')}}: </strong> {{isset($profile_info->vat) ? $profile_info->vat : 'Non disponibile'}}
        </li>
        <li>
            <strong>{{L::t('Shipping address')}}: </strong> {{isset($profile_info->shipping_address) ? $profile_info->shipping_address : 'Non disponibile'}}
            , {{isset($profile_info->shipping_city) ? $profile_info->shipping_city : 'Non disponibile'}}
            , {{isset($profile_info->shipping_address_zip) ? $profile_info->shipping_address_zip : 'Non disponibile'}}
            , {{isset($profile_info->shipping_country) ? $profile_info->shipping_country : 'Non disponibile'}}
            , {{isset($profile_info->shipping_state) ? $profile_info->shipping_state : 'Non disponibile'}}
        </li>
    </ul>
    <br>
    <a href="{{URL::to('/')}}" target="_blank">Homepage {{Config::get('authentication::app_name')}}</a>
</div>
</body>
</html>
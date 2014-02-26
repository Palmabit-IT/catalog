@extends('catalog::layouts.base-2-cols-multilanguage')

@section('title')
    {{$app_name}} Admin area: insersci categorie
@stop

@section('content')
    {{-- bootstrap 3 tabs --}}
    @include('catalog::category.tabs')
    <div class="tab-content">
        <div class="tab-pane fade in active" id="tab-generale">
            @include('catalog::category.general')
        </div>
    </div>
@stop

@section('footer_scripts')
@parent
    {{ HTML::script('packages/palmabit/catalog/js/salva-tab.js') }}
@stop
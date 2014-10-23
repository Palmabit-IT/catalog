@extends('catalog::layouts.base-2-cols-multilanguage')

@section('title')
{{$app_name}} {{L::t('Admin area: insersci prodotto')}}
@stop

@section('content')
    <div class="top-product-flags pull-right">
        {{$product->presenter()->availableflags}}
    </div>
    {{-- bootstrap 3 tabs --}}
    @include('catalog::products.tabs')
    <div class="tab-content">
    <div class="tab-pane fade in active" id="tab-generale">
        @if(\L::get_admin() == L::getDefault())
            @include('catalog::products.general-full')
        @else
            @include('catalog::products.general-simple')
        @endif
    </div>
    <div class="tab-pane fade" id="tab-categoria">
        @include('catalog::products.category')
    </div>
    <div class="tab-pane fade" id="tab-immagini">
        @include('catalog::products.image')
    </div>
    <div class="tab-pane fade" id="tab-accessories">
        @include('catalog::products.accessories')
    </div>
    </div>
@stop

@section('footer_scripts')
@parent
    {{ HTML::script('packages/palmabit/catalog/js/save-tab.js') }}
@stop
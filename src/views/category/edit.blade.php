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
        <div class="tab-pane fade in" id="tab-tree">
            @include('catalog::category.tree')
        </div>
        <div class="tab-pane fade in" id="tab-image">
            @include('catalog::category.image')
        </div>
    </div>
@stop

@section('footer_scripts')
@parent
{{ HTML::script('packages/palmabit/catalog/js/save-tab.js') }}
@stop
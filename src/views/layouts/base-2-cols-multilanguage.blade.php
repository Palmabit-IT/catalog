@extends('authentication::layouts.base')

@section('head_css')
    @parent
    {{ HTML::style('packages/palmabit/catalog/css/catalog.css') }}
@stop

@section('container')
<div class="row">
    <div class="col-md-2 nav bs-sidenav">
        @include('authentication::layouts.sidebar')
    </div>
    <div class="col-md-10">
    {{-- here put the admin language change select --}}
        @yield('content')
    </div>
</div>
@stop
@section('footer_scripts')
<script>
    $( document ).ready(function() {
        $("#select-lang").change(function(){
            $("#form-select-lang").submit()
        });
    });
</script>
@stop
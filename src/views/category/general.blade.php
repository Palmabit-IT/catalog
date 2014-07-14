<div class="row">
    <div class="col-md-12">

		{{-- messaggi vari --}}
		<?php $message = Session::get('message'); ?>
		@if( isset($message) )
		<div class="alert alert-success">{{$message}}</div>
		@endif

		<h3>{{ isset($categories->id) ? 'Modifica categoria' : 'Aggiungi nuova categoria' }}</h3>
		{{Form::model($categories, ['url' => URL::action('Palmabit\Catalog\Controllers\CategoryController@postEdit'), 'method' => 'post'] )}}
		{{FormField::name(["id" => "slugme", "type" => "text"])}}
		{{Form::hidden('id')}}

		<hr>
		{{Form::submit('Salva', array("class"=>"btn btn-large btn-primary tab-remember"))}}
        {{Form::updateOldLanguageInput()}}

        {{Form::close()}}

	</div>

    @include('catalog::category.description')

</div>


@section('footer_scripts')
@parent
<script>
    $("#form-select-lang").hide();
</script>
@stop
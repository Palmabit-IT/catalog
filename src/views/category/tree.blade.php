<div class="row">
    <div class="col-md-12">

		{{-- messaggi vari --}}
		<?php $message = Session::get('message_tree'); ?>
		@if( isset($message) )
			<div class="alert alert-success">{{$message}}</div>
		@endif

		@if($errors && ! $errors->isEmpty() )
			@foreach($errors->all() as $error)
				<div class="alert alert-danger">{{$error}}</div>
			@endforeach
		@endif

		<h3>Modifica padre</h3>
		{{Form::open(['action' => 'Palmabit\Catalog\Controllers\CategoryController@postSetParent', 'method' => 'post'])}}
	        <div class="form-group">
	            {{Form::label("categoria","Seleziona la categoria padre:")}}
	            {{Form::select("parent_id", get_cat_select_arr(true), $categories->parent_id, ["class" => "form-control"]) }}
	        </div>
	        {{Form::hidden("id", $categories->id)}}
	        {{Form::hidden("slug_lang", $slug_lang)}}

			<hr>
			{{Form::submit('Salva', ['class' => 'btn btn-primary tab-remember'])}}
		{{Form::close()}}

	</div>
</div>
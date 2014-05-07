<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Ricerca prodotti</h3>
    </div>
    <div class="panel-body">
        {{Form::open(['action' => 'Palmabit\Catalog\Controllers\ProductsController@lists','method' => 'get'])}}
        {{FormField::code(['label' => 'Codice: '])}}
        {{FormField::name(['label' => 'Nome: '])}}
        <div class="form-group">
            {{Form::label('featured', 'In evidenza: ')}}
            {{Form::select('featured', ['' => '', 1 => 'Sì', 0 => 'No'], Input::get('featured',''), ["class" => "form-control"])}}
        </div>
        <div class="form-group">
            {{Form::label('public', 'Pubblico: ')}}
            {{Form::select('public', ['' => '', 1 => 'Sì', 0 => 'No'], Input::get('public',''), ["class" => "form-control"])}}
        </div>
        <div class="form-group">
            {{Form::label('offer', 'In offerta: ')}}
            {{Form::select('offer', ['' => '', 1 => 'Sì', 0 => 'No'], Input::get('offer',''), ["class" => "form-control"])}}
        </div>
        <div class="form-group">
            {{Form::label('professional', 'Professionale: ')}}
            {{Form::select('professional', ['' => '', 1 => 'Sì', 0 => 'No'], Input::get('professional',''), ["class" => "form-control"])}}
        </div>
        <div class="form-group margin-bottom-20">
            {{Form::label('category_id', 'Categoria: ')}}
            {{Form::select('category_id', \App::make('category_repository')->getArrSelectCat(), Input::get('category_id',''), ["class" => "form-control"])}}
        </div>
        <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@lists')}}" class="btn btn-default">Pulisci</a>
        {{Form::submit('Cerca', ["class" => "btn btn-primary"])}}
        {{Form::close()}}
    </div>
</div>
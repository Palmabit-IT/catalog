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
        {{Form::reset('Pulisci', ["class" => "btn btn-default"])}}
        {{Form::submit('Cerca', ["class" => "btn btn-primary"])}}
        {{Form::close()}}
    </div>
</div>
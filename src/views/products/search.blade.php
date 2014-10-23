<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="glyphicon glyphicon-search"></i> {{L::t('Ricerca prodotti')}}</h3>
    </div>
    <div class="panel-body">
        {{Form::open(['action' => 'Palmabit\Catalog\Controllers\ProductsController@lists','method' => 'get'])}}
        {{Form::label('code', L::t('Codice: ') )}}
        {{Form::text('code', Input::get('code'), ['class' => 'form-control'])}}
        {{Form::label('name', L::t('Nome: ') )}}
        {{Form::text('name', Input::get('name'), ['class' => 'form-control'])}}
        <div class="form-group">
            {{Form::label('featured', L::t('In evidenza: '))}}
            {{Form::select('featured', ['' => '', 1 => 'Sì', 0 => 'No'], Input::get('featured',''), ["class" => "form-control"])}}
        </div>
        <div class="form-group">
            {{Form::label('public', L::t('Pubblico: '))}}
            {{Form::select('public', ['' => '', 1 => 'Sì', 0 => 'No'], Input::get('public',''), ["class" => "form-control"])}}
        </div>
        <div class="form-group">
            {{Form::label('offer', L::t('In offerta: '))}}
            {{Form::select('offer', ['' => '', 1 => 'Sì', 0 => 'No'], Input::get('offer',''), ["class" => "form-control"])}}
        </div>
        <div class="form-group">
            {{Form::label('professional', L::t('Professionale: '))}}
            {{Form::select('professional', ['' => '', 1 => 'Sì', 0 => 'No'], Input::get('professional',''), ["class" => "form-control"])}}
        </div>
        <div class="form-group margin-bottom-20">
            {{Form::label('category_id', L::t('Categoria: ') )}}
            {{Form::select('category_id', \App::make('category_repository',true)->getArrSelectCat(), Input::get('category_id',''), ["class" => "form-control"])}}
        </div>
        <a href="{{URL::action('Palmabit\Catalog\Controllers\ProductsController@lists')}}" class="btn btn-default">{{L::t('Pulisci')}}</a>
        {{Form::submit(L::t('Cerca'), ["class" => "btn btn-primary"])}}
        {{Form::close()}}
    </div>
</div>
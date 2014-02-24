<?php namespace Validators;

class ImmaginiProdottoValidator extends AbstractValidator
{
    protected static $rules = array(
        "descrizione" => "max:255",
        'immagine' => ['required','image','min:1', 'max:4096']
    );
}
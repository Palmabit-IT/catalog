<?php namespace Prodotti\Controllers;

use Classes\FormModel;
use Prodotti\Repository\TagsRepository;
use Validators\TagsValidator;
use BaseController;
use Input;
use Redirect;

class TagsController extends BaseController {

    /**
     * Repository per i tags
     * @var \Prodotti\Reposityory\TagsRepository
     */
    protected $r;
    /**
     * Il validatore
     * @var \Validators\TagsValidator
     */
    protected $v;

    function __construct(TagsRepository $r, TagsValidator $v)
    {
        $this->r = $r;
        $this->v = $v;
        $this->f = new FormModel($this->v, $this->r);

    }

    public function creaTag()
    {
        $slug_lingua= Input::get('slug_lingua');
        $input = Input::all();

        try
        {
            $this->f->process($input);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->withErrors($this->f->getErrors());
        }

        return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->with(array("message_tag"=>"Tag creato con successo."));
    }

    public function associaTag()
    {
        $slug_lingua= Input::get('slug_lingua');
        $input = Input::all();

        try
        {
            $this->f->process($input);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->withErrors($this->f->getErrors());
        }

        return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->with(array("message_tag"=>"Tag asociato con successo."));
    }

    public function cancellaTag()
    {
        $slug_lingua= Input::get('slug_lingua');
        $input = Input::all();

        try
        {
            $this->f->delete($input);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->withErrors($this->f->getErrors());
        }

        return Redirect::action("Prodotti\\Controllers\\ProdottoController@getModifica",["slug_lingua" => $slug_lingua])->with(array("message_tag"=>"Tag cancellato con successo."));
    }

}

<?php namespace Category\Controllers;

use BaseController;
use View;
use Input;
use Redirect;
use Category\Repository\CategoriaRepository;
use Exceptions\PalmabitExceptionsInterface;
use Classes\FormModel;
use Validators\CategoriaValidator;
use Categoria;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Presenters\PresenterCategoria;

class CategoryController extends BaseController
{
    /**
     * Repository per la gestione delle categorie
     * @var
     */
    protected $repo;
    /**
     * Validatore categoria
     *
     * @var CategoriaValidator
     */
    protected $v;
    /**
     * Model per il salvataggio del form
     * @var \Classes\FormModel
     */
    protected $f;
    /**
     * Presenter categoria
     * @var \Presenters\PresenterCategoria
     */
    protected $p;

    public function __construct(CategoriaRepository $repo, CategoriaValidator $v)
    {
        $is_admin = true;
        $this->repo = new CategoriaRepository($is_admin);
        $this->v = $v;
        $this->f = new FormModel($v, $repo);
    }

    public function lista()
    {
        $cats = $this->repo->all();

        return View::make('admin.category.show')->with( array("categorie" => $cats) );
    }

    public function getModifica()
    {
        $slug_lingua = Input::get('slug_lingua');

        try
        {
            $categorie = $this->repo->findBySlugLingua($slug_lingua);

        }
        catch(ModelNotFoundException $e)
        {
            $categorie = new Categoria();
        }
        $this->p = new PresenterCategoria($categorie);

        return View::make('admin.category.modifica')->with( ["categorie" => $categorie, "slug_lingua" => $slug_lingua, "presenter" => $this->p] );
    }

    public function postModifica()
    {
       $input = Input::all();

       try
       {
           $obj = $this->f->process($input);
       }
       catch(PalmabitExceptionsInterface $e)
       {
           $errors = $this->f->getErrors();
           return Redirect::action("Category\\Controllers\\CategoryController@getModifica")->withInput()->withErrors($errors);
       }

       return Redirect::action("Category\\Controllers\\CategoryController@getModifica",["slug_lingua" => $obj->slug_lingua])->with(["message"=>"Categoria modificata con successo."]);
    }

    public function cancella()
    {
        $input = Input::all();

        try
        {
            $this->f->delete($input);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::action("Category\\Controllers\\CategoryController@lista")->withErrors($errors);
        }

        return Redirect::action("Category\\Controllers\\CategoryController@lista")->with(array("message"=>"Categoria eliminata con successo."));
    }

    public function associaAccessorio()
    {
        $categoria_id = Input::get('categoria_id');
        $accessorio_id = Input::get('accessorio_id');
        $slug_lingua= Input::get('slug_lingua');

        try
        {
            $this->repo->associaAccessorio($categoria_id, $accessorio_id);
        }
        catch(ModelNotFoundException $e)
        {
            return Redirect::action("Category\\Controllers\\CategoryController@getModifica", ["slug_lingua" => $slug_lingua])->withErrors(new MessageBag(["model" => "Accessorio non trovato."]));
        }

        return Redirect::action("Category\\Controllers\\CategoryController@getModifica",["slug_lingua" => $slug_lingua])->with(array("message_acc"=>"Accessorio associato con successo."));
    }

    public function deassociaAccessorio()
    {
        $categoria_id = Input::get('categoria_id');
        $accessorio_id = Input::get('accessorio_id');
        $slug_lingua= Input::get('slug_lingua');

        try
        {
            $this->repo->deassociaAccessorio($categoria_id, $accessorio_id);
        }
        catch(ModelNotFoundException $e)
        {
            return Redirect::action("Category\\Controllers\\CategoryController@getModifica", ["slug_lingua" => $slug_lingua])->withErrors(new MessageBag(["model" => "Accessorio non trovato."]));
        }

        return Redirect::action("Category\\Controllers\\CategoryController@getModifica",["slug_lingua" => $slug_lingua])->with(array("message_acc"=>"Accessorio deassociato con successo."));
    }

}
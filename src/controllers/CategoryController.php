<?php namespace Palmabit\Catalog\Controllers;

use BaseController, View, Input, Redirect, App;
use Exceptions\PalmabitExceptionsInterface;
use Palmabit\Library\Form\FormModel;
use Palmabit\Catalog\Validators\CategoryValidator;
use Palmabit\Catalog\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Palmabit\Catalog\Presenters\PresenterCategory;

class CategoryController extends BaseController
{
    /**
     * Repository for the categories
     * @var
     */
    protected $repo;
    /**
     * Category validator
     *
     * @var CategoryValidator
     */
    protected $v;
    /**
     * @var Palmabit\Library\Form\FormModel
     */
    protected $f;
    /**
     * @var Palmabit\Catalog\Presenters\PresenterCategoria
     */
    protected $p;

    public function __construct(CategoryValidator $v)
    {
        $is_admin = true;
        $this->repo = App::make('category_repository', $is_admin);
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
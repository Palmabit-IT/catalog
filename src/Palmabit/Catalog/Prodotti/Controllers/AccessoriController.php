<?php namespace Prodotti\Controllers;

use BaseController;
use Prodotti\Repository\AccessoriRepository;
use Validators\AccessoriValidator;
use Classes\FormModel;
use View;
use Redirect;
use Accessori;
use Input;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exceptions\PalmabitExceptionsInterface;

class AccessoriController extends BaseController {

    /**
     * @var \Prodotti\Repository\AccessoriRepository
     */
    protected $r;
    /**
     * @var \Validators\AccessoriValidator
     */
    protected $v;
    /**
     * @var \Classes\FormModel
     */
    protected $f;

    public function __construct(AccessoriRepository $r, AccessoriValidator $v)
    {
        $this->r = $r;
        $this->v = $v;
        $this->f = new FormModel($this->v, $this->r);
    }

    public function lista()
    {
        $accessori = $this->r->all();
        return View::make('admin.accessori.show')->with(array("accessori" => $accessori));
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
            return Redirect::action("Prodotti\\Controllers\\AccessoriController@getModifica")->withInput()->withErrors($errors);
        }

        return Redirect::action("Prodotti\\Controllers\\AccessoriController@getModifica",["slug_lingua" => $obj->slug_lingua])->with(["message"=>"Accessorio modificato con successo."]);
    }

    public function getModifica()
    {
        $slug_lingua = Input::get('slug_lingua');

        try
        {
            $accessori = $this->r->findBySlugLingua($slug_lingua);
        }
        catch(ModelNotFoundException $e)
        {
            $accessori = new Accessori();
        }

        return View::make('admin.accessori.modifica')->with( array("accessori" => $accessori, "slug_lingua" => $slug_lingua) );
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
            return Redirect::action("Prodotti\\Controllers\\AccessoriController@lista")->withErrors($errors);
        }

        return Redirect::action("Prodotti\\Controllers\\AccessoriController@lista")->with(array("message"=>"Accessorio eliminato con successo."));
    }

}

<?php

namespace FoxEngineers\AdminCP\Http\Controllers;

use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;

abstract class ResourceController extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    private $perPage = 15;
    private $_baseRoute;
    private $_methods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
    private $_view;

    protected $storeValidate = [];

    protected $updateValidate = [];

    protected $loadDataForStore = [];

    protected $loadDataForUpdate = [];

    public function dataForStore()
    {
        return [];
    }

    public function dataForUpdate()
    {
        return [];
    }


    public function setDataForStore()
    {
        $this->loadDataForStore = $this->dataForStore();
    }

    public function setDataForUpdate()
    {
        $this->loadDataForUpdate = $this->dataForUpdate();
    }

    public function getDataForStore()
    {
        return $this->loadDataForStore;
    }

    public function getDataForUpdate()
    {
        return $this->loadDataForUpdate;
    }

    /**
     * @return string
     */
    abstract public function baseRoute();

    public function setBaseRoute()
    {
        $this->_baseRoute = $this->baseRoute();
    }

    public function getBaseRoute()
    {
        return $this->_baseRoute;
    }

    /**
     * @return string
     */
    abstract public function view();

    public function setView()
    {
        $this->_view = $this->view();
    }

    public function getView()
    {
        return $this->_view;
    }

    /**
     * @return array
     */
    abstract public function storeValidate();

    public function setStoreValidate()
    {
        $this->storeValidate = $this->storeValidate();
    }

    public function getStoreValidate()
    {
        return $this->storeValidate;
    }

    abstract public function updateValidate($id);

    public function setUpdateValidate($id)
    {
        $this->updateValidate = $this->updateValidate($id);
    }

    public function getUpdateValidate()
    {
        return $this->updateValidate;
    }

    /** @var BaseRepository $repository */
    protected $repository;

    abstract public function repository();

    /**
     * @return mixed
     * @throws BindingResolutionException
     */
    public function makeRepository()
    {
        $repository = app()->make($this->repository());
        return $this->repository = $repository;
    }

    /**
     * ResourceController constructor.
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->makeRepository();
        $this->setBaseRoute();
        $this->setView();
    }

    public function getPerPage()
    {
        return $this->perPage;
    }

    public function fetchData()
    {
        return $this->repository->orderBy('id', 'DESC')->paginate($this->getPerPage());
    }

    public function index()
    {
        $data = $this->fetchData();
        return view($this->getView() . '.index', compact('data'));
    }

    public function create()
    {
        $this->setDataForStore();
        $arr = $this->getDataForStore();
        return view($this->getView() . '.create', $arr);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->setStoreValidate();
        $data = $this->validate($request, $this->getStoreValidate());
        try {
            $this->repository->create($data);
            return redirect()->route($this->getBaseRoute())->withFlashSuccess(__('strings.backend.crud.create.success'));
        } catch (\Exception $e) {
            return redirect()->route($this->getBaseRoute())->withFlashDanger(__('strings.backend.crud.create.failed') . '. ' . $e->getMessage());
        }
    }


    public function show($id)
    {
        $data = $this->repository->find($id);
        return view($this->getView() . '.show', compact('data'));
    }


    public function edit($id)
    {
        $this->setDataForUpdate();
        $arr = $this->getDataForUpdate();
        $arr['data'] = $this->repository->find($id);
        return view($this->getView() . '.edit', $arr);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        $this->setUpdateValidate($id);
        $data = $this->validate($request, $this->getUpdateValidate());
        try {
            $this->repository->update($data, $id);
            return redirect()->route($this->getBaseRoute())->withFlashSuccess(__('strings.backend.crud.update.success'));
        } catch (\Exception $e) {
            return redirect()->route($this->getBaseRoute())->withFlashDanger(__('strings.backend.crud.update.failed') . '. ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->repository->delete($id);
            return redirect()->route($this->getBaseRoute())->withFlashSuccess(__('strings.backend.crud.delete.success'));
        } catch (\Exception $e) {
            return redirect()->route($this->getBaseRoute())->withFlashDanger(__('strings.backend.crud.delete.failed') . '. ' . $e->getMessage());
        }
    }
}

<?php

namespace FoxEngineers\AdminCP\Http\Controllers;

use FoxEngineers\AdminCP\Helpers\Traits\Searchable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\ValidationException;
use Prettus\Repository\Eloquent\BaseRepository;

abstract class ResourceControllerCrud extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use Searchable;
    use ValidatesRequests;

    public const LEFT = 0;
    public const RIGHT = 1;
    public const CENTER = 2;

    public const OFF = 0;
    public const ON = 1;

    public $name;

    public $primaryKey = 'id';

    public $isUseImport = false;

    public $_methods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

    public $_crudView = false;

    protected $storeValidate = [];

    protected $updateValidate = [];

    protected $importValidate = [];

    protected $loadDataForStore = [];

    protected $loadDataForUpdate = [];

    /** @var BaseRepository */
    protected $repository;

    private $perPage = 15;

    /**
     * ResourceControllerCrud constructor.
     *
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->makeRepository();
    }

    abstract public function repository();

    public function route()
    {
        return 'admin.'.$this->getName();
    }

    public function getName()
    {
        return $this->name;
    }

    public function title()
    {
        return __('text.'.$this->getName().'.management');
    }

    public function formData()
    {
        return [];
    }

    public function importFormData()
    {
        return [];
    }

    public function formDataForImport()
    {
        return [];
    }

    public function columns()
    {
        return [];
    }

    public function titleForCreate()
    {
        return __('labels.general.buttons.create');
    }

    public function titleForUpdate()
    {
        return __('labels.general.buttons.update');
    }

    public function buttonCreate()
    {
        return __('buttons.general.crud.create');
    }

    public function buttonUpdate()
    {
        return __('buttons.general.crud.update');
    }

    public function customButton(string $name)
    {
        return __('buttons.general.crud.'.$name);
    }

    public function crudView()
    {
        return 'backend._form.crud';
    }

    public function crudViewTable()
    {
        return 'backend._form.index';
    }

    public function getImport()
    {
        $this->setDataForImport();
        $arr = $this->getDataForImport();

        return view($this->crudView(), $arr);
    }

    public function view()
    {
        return '';
    }

    public function dataForStore()
    {
        return [];
    }

    public function dataForUpdate()
    {
        return [];
    }

    public function useCrudView()
    {
        return $this->_crudView;
    }

    public function getDataForStore()
    {
        return $this->loadDataForStore;
    }

    public function getDataForUpdate()
    {
        return $this->loadDataForUpdate;
    }

    public function getDataForImport()
    {
        return $this->loadDataForImport;
    }

    /**
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    public function makeRepository()
    {
        $repository = app()->make($this->repository());

        return $this->repository = $repository;
    }

    abstract public function storeValidate();

    public function setStoreValidate(): void
    {
        $this->storeValidate = $this->storeValidate();
    }

    public function getStoreValidate()
    {
        return $this->storeValidate;
    }

    abstract public function updateValidate($id);

    public function setUpdateValidate($id): void
    {
        $this->updateValidate = $this->updateValidate($id);
    }

    public function getUpdateValidate()
    {
        return $this->updateValidate;
    }

    public function importValidate()
    {
        return [
            'file' => 'required|mimes:xlsx,xls',
        ];
    }

    public function setImportValidate(): void
    {
        $this->importValidate = $this->importValidate();
    }

    public function getImportValidate()
    {
        return $this->importValidate;
    }

    public function getPerPage()
    {
        return $this->perPage;
    }

    public function fetchData($params = [], $relations = [])
    {
        $key = $this->primaryKey;
        $orderBy = 'DESC';
        $s = null;
        $searchLike = true;

        $data = $this->repository->getModel();

        if (!empty($params)) {
            $key = $params['key'];
            if (\in_array($params['orderBy'], ['desc', 'asc'], true)) {
                $orderBy = $params['orderBy'];
            }
            $s = $params['s'];
            $searchLike = $params['searchLike'] ?? true;
        }

        $data = $data->orderBy($key, $orderBy);

        if ($params && $s) {
            if (true === $searchLike) {
                $data = $data->where($key, 'LIKE', '%'.$s.'%');
            } else {
                $data = $data->where($key, $s);
            }
        }

        if (\is_array($relations) && !empty($relations)) {
            $data = $data->with($relations);
        }

        return $data->paginate($this->getPerPage());
    }

    public function index()
    {
        $params = [];

        if (request()->has('k')) {
            if (\in_array(request()->get('k'), $this->getSearch(), true)) {
                $params['key'] = request()->get('k');
                $params['searchLike'] = $this->isSearchLike($params['key']);
                if (request()->has('s')) {
                    $params['s'] = request()->get('s');
                }
                if (request()->has('sort') && \in_array(request()->get('sort'), ['desc', 'asc'], true)) {
                    $params['orderBy'] = request()->get('sort');
                }
            }
        }

        $data['data'] = $this->fetchData($params);
        $data['searchFields'] = $this->getSearch();
        $view = $this->view().'.index';
        if ($this->useCrudView()) {
            $data['columns'] = $this->columns();
            $data['canCreate'] = (\in_array('create', $this->_methods, true)) ? true : false;
            $data['canUpdate'] = (\in_array('edit', $this->_methods, true)) ? true : false;
            $data['canDelete'] = (\in_array('destroy', $this->_methods, true)) ? true : false;
            $data['canImport'] = $this->isUseImport;
            $data['route'] = $this->route();
            $data['title'] = $this->title();
            $data['primaryKey'] = $this->primaryKey;
            $view = $this->crudViewTable();
        }

        return view($view, $data);
    }

    public function create()
    {
        $this->setDataForStore();
        $arr = $this->getDataForStore();
        $view = ($this->_crudView) ? $this->crudView() : $this->view().'.create';

        return view($view, $arr);
    }

    /**
     * @return mixed
     *
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
            logger($e);

            return redirect()->route($this->getBaseRoute())->withFlashDanger(__('strings.backend.crud.create.failed').'. '.$e->getMessage());
        }
    }

    public function show($id, $relations = [])
    {
        $query = $this->repository;
        if (\is_array($relations) && !empty($relations)) {
            $query = $query->with($relations);
        }
        $data = $query->find($id);
        $view = $this->view().'.show';

        return view($view, compact('data'));
    }

    public function edit($id)
    {
        $this->setDataForUpdate($id);
        $arr = $this->getDataForUpdate();
        $arr['data'] = $this->repository->find($id);
        $view = ($this->_crudView) ? $this->crudView() : $this->view().'.edit';

        return view($view, $arr);
    }

    /**
     * @return mixed
     *
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
            logger($e);

            return redirect()->route($this->getBaseRoute())->withFlashDanger(__('strings.backend.crud.update.failed').'. '.$e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->repository->delete($id);

            return redirect()->route($this->getBaseRoute())->withFlashSuccess(__('strings.backend.crud.delete.success'));
        } catch (\Exception $e) {
            logger($e);

            return redirect()->route($this->getBaseRoute())->withFlashDanger(__('strings.backend.crud.delete.failed').'. '.$e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $this->setImportValidate();
        $data = $this->validate($request, $this->getImportValidate());

        try {
            $this->executeImport($data['file']);

            return redirect()->route($this->getBaseRoute())->withFlashSuccess(__('strings.backend.crud.import.success'));
        } catch (\Exception $e) {
            logger($e);

            return redirect()->route($this->getBaseRoute())->withFlashDanger(__('strings.backend.crud.import.failed').'. '.$e->getMessage());
        }
    }

    public function executeImport(UploadedFile $file): void
    {
        // To do import.
    }

    private function getBaseRoute()
    {
        return $this->route().'.index';
    }

    private function setDataForStore(): void
    {
        $data = $this->dataForStore();
        if ($this->useCrudView()) {
            if (!isset($data['formData'])) {
                $data['formData'] = [];
            }
            $data['formData']['title'] = $this->title();
            $data['formData']['subTitle'] = $this->titleForCreate();
            $data['formData']['inputs'] = $this->formData();
            $data['formData']['method'] = 'POST';
            $data['formData']['route'] = route($this->route().'.store');
            $data['formData']['cancel'] = route($this->getBaseRoute());
            $data['formData']['submit'] = $this->buttonCreate();
        }
        $this->loadDataForStore = $data;
    }

    private function setDataForUpdate($id): void
    {
        $data = $this->dataForUpdate();
        if ($this->useCrudView()) {
            if (!isset($data['formData'])) {
                $data['formData'] = [];
            }
            $data['formData']['title'] = $this->title();
            $data['formData']['subTitle'] = $this->titleForUpdate();
            $data['formData']['inputs'] = $this->formData();
            $data['formData']['method'] = 'PUT';
            $data['formData']['route'] = route($this->route().'.update', $id);
            $data['formData']['cancel'] = route($this->getBaseRoute());
            $data['formData']['submit'] = $this->buttonUpdate();
        }
        $this->loadDataForUpdate = $data;
    }

    private function setDataForImport(): void
    {
        $data = [];
        if ($this->isUseImport) {
            $data['formData']['title'] = $this->title();
            $data['formData']['subTitle'] = 'Import';
            $data['formData']['inputs'] = $this->importFormData();
            $data['formData']['method'] = 'POST';
            $data['formData']['enctype'] = 'multipart';
            $data['formData']['route'] = route($this->route().'.import.post');
            $data['formData']['cancel'] = route($this->getBaseRoute());
            $data['formData']['submit'] = $this->customButton('import');
        }
        $this->loadDataForImport = $data;
    }
}

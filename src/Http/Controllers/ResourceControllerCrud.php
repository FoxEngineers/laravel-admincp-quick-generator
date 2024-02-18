<?php

namespace FoxEngineers\AdminCP\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Prettus\Repository\Eloquent\BaseRepository;
use FoxEngineers\AdminCP\Helpers\Traits\Searchable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class ResourceControllerCrud extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
    use Searchable;

    public $name;

    public $primaryKey = 'id';

    private $perPage = 15;

    public $isUseImport = false;

    public $isUseExport = false;

    public $_methods = [
        'index',
        'create',
        'store',
        'show',
        'edit',
        'update',
        'destroy',
    ];

    public $_crudView = false;

    protected $storeValidate = [];

    protected $updateValidate = [];

    protected $importValidate = [];

    protected $loadDataForStore = [];

    protected $loadDataForUpdate = [];

    const LEFT = 0;
    const RIGHT = 1;
    const CENTER = 2;

    const OFF = 0;
    const ON = 1;

    /** @var BaseRepository $repository */
    protected $repository;

    protected ?string $importClass;

    /**
     * ResourceControllerCrud constructor.
     *
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->makeRepository();
        $this->init();
    }

    abstract public function repository();

    public function init(): void {
        // Init code here.
    }

    public function route()
    {
        return 'admin.' . $this->getName();
    }

    public function getName()
    {
        return $this->name;
    }

    public function title()
    {
        return __('text.' . $this->getName() . '.management');
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
        return __('buttons.general.crud.' . $name);
    }

    private function getBaseRoute()
    {
        return $this->route() . '.index';
    }

    public function crudView()
    {
        return 'laravel-admincp::backend._form.crud';
    }

    public function crudViewTable()
    {
        return 'laravel-admincp::backend._form.index';
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

    private function setDataForStore()
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
            $data['formData']['route'] = route($this->route() . '.store');
            $data['formData']['cancel'] = route($this->getBaseRoute());
            $data['formData']['submit'] = $this->buttonCreate();
        }
        $this->loadDataForStore = $data;
    }

    private function setDataForUpdate($id)
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
            $data['formData']['route'] = route($this->route() . '.update', $id);
            $data['formData']['cancel'] = route($this->getBaseRoute());
            $data['formData']['submit'] = $this->buttonUpdate();
        }
        $this->loadDataForUpdate = $data;
    }

    private function setDataForImport()
    {
        $data = [];
        if ($this->isUseImport) {
            $data['formData']['title'] = $this->title();
            $data['formData']['subTitle'] = 'Import';
            $data['formData']['inputs'] = $this->importFormData();
            $data['formData']['method'] = 'POST';
            $data['formData']['enctype'] = 'multipart';
            $data['formData']['route'] = route($this->route() . '.import.post');
            $data['formData']['cancel'] = route($this->getBaseRoute());
            $data['formData']['submit'] = $this->customButton('import');
        }
        $this->loadDataForImport = $data;
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
     * @throws BindingResolutionException
     */
    public function makeRepository()
    {
        $repository = app()->make($this->repository());
        return $this->repository = $repository;
    }

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

    public function importValidate()
    {
        return [
            'file' => 'required|mimes:xlsx,xls',
        ];
    }

    public function setImportValidate()
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
            if (in_array($params['orderBy'], ['desc', 'asc'])) {
                $orderBy = $params['orderBy'];
            }
            $s = $params['s'];
            $searchLike = $params['searchLike'] ?? true;
        }

        $data = $data->orderBy($key, $orderBy);

        if ($params && $s) {
            if ($searchLike) {
                $data = $data->where($key, 'LIKE', '%' . $s . '%');
            } else $data = $data->where($key, $s);
        }

        if (is_array($relations) && !empty($relations)) {
            $data = $data->with($relations);
        }


        $data = $data->paginate($this->getPerPage());

        return $data;
    }

    public function index()
    {
        $params = [];

        if (request()->has('k')) {
            if (in_array(request()->get('k'), $this->getSearch())) {
                $params['key'] = request()->get('k');
                $params['searchLike'] = $this->isSearchLike($params['key']);
                if (request()->has('s')) {
                    $params['s'] = request()->get('s');
                }
                if (request()->has('sort') && in_array(request()->get('sort'), ['desc', 'asc'])) {
                    $params['orderBy'] = request()->get('sort');
                }
            }
        }

        $data['data'] = $this->fetchData($params);
        $data['searchFields'] = $this->getSearch();
        $view = $this->view() . '.index';
        if ($this->useCrudView()) {
            $data['columns'] = $this->columns();
            $data['canCreate'] = in_array('create', $this->_methods);
            $data['canUpdate'] = in_array('edit', $this->_methods);
            $data['canDelete'] = in_array('destroy', $this->_methods);
            $data['canImport'] = $this->isUseImport;
            $data['canExport'] = $this->isUseExport;
            $data['route'] = $this->route();
            $data['title'] = $this->title();
            $data['primaryKey'] = $this->primaryKey;
            $data['additionalActions'] = $this->additionalActions();
            $view = $this->crudViewTable();
        }
        return view($view, $data);
    }

    public function create()
    {
        $this->setDataForStore();
        $arr = $this->getDataForStore();
        $view = ($this->_crudView) ? $this->crudView() : $this->view() . '.create';
        return view($view, $arr);
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
            logger($e);
            return redirect()->route($this->getBaseRoute())->withFlashDanger(__('strings.backend.crud.create.failed') . '. ' . $e->getMessage());
        }
    }

    public function show($id, $relations = [])
    {
        $query = $this->repository;
        if (is_array($relations) && !empty($relations)) {
            $query = $query->with($relations);
        }
        $data = $query->find($id);
        $view = $this->view() . '.show';
        return view($view, compact('data'));
    }

    public function edit($id)
    {
        $this->setDataForUpdate($id);
        $arr = $this->getDataForUpdate();
        $arr['data'] = $this->repository->find($id);
        $view = ($this->_crudView) ? $this->crudView() : $this->view() . '.edit';
        return view($view, $arr);
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
            logger($e);
            return redirect()->route($this->getBaseRoute())->withFlashDanger(__('strings.backend.crud.update.failed') . '. ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->repository->delete($id);
            return redirect()->route($this->getBaseRoute())->withFlashSuccess(__('strings.backend.crud.delete.success'));
        } catch (\Exception $e) {
            logger($e);
            return redirect()->route($this->getBaseRoute())->withFlashDanger(__('strings.backend.crud.delete.failed') . '. ' . $e->getMessage());
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
            return redirect()->route($this->getBaseRoute())->withFlashDanger(__('strings.backend.crud.import.failed') . '. ' . $e->getMessage());
        }
    }

    public function executeImport(UploadedFile $file)
    {
        if ($this->importClass == null) {
            return;
        }
        $importClass = resolve($this->importClass);
        Excel::import($importClass, $file);
    }

    public function getExportFileName(): string
    {
        return $this->getName();
    }

    public function getExportData(): ?FromCollection
    {
        return null;
    }

    public function getExportFileType(): string
    {
        return 'xlsx';
    }

    public function export(Request $request)
    {
        $data = $this->getExportData();
        if (!$data instanceof FromCollection) {
            return redirect()->route($this->getBaseRoute());
        }
        try {
            $now = Carbon::now();
            $prefix = $now->format('Y_m_d_H_i_s');
            $fileName = $this->getExportFileName() . '_' . $prefix
                . '.' . $this->getExportFileType();
            return Excel::download($data, $fileName);
        } catch (\Exception $e) {
            logger($e);
            return redirect()->route($this->getBaseRoute())
                ->withFlashDanger(__('strings.backend.crud.export.failed') . '. ' . $e->getMessage());
        }
    }

    /**
     * Example:
     * [
     *  [ 'url' => '', 'title' => '', 'icon' => '']
     * ]
     * @return array
     */
    public function additionalActions(): array
    {
        return [];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Job;
use App\Company;
use App\Helper\Files;
use App\Helper\Reply;
use GuzzleHttp\Psr7\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Company\StoreCompany;
use App\Http\Requests\Company\UpdateCompany;

class AdminCompanyController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('menu.companies');
        $this->pageIcon = 'icon-badge';
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        
        abort_if(!$this->user->cans('view_company'), 403);

        $this->totalCompanies = Company::count();
        $this->activeCompanies = Company::where('status', 'active')->count();
        $this->inactiveCompanies = Company::where('status', 'inactive')->count();
        return view('admin.company.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->cans('add_company'), 403);
        return view('admin.company.create', $this->data);
    }

    /**
     * @param StoreCompany $request
     * @return array
     * @throws \Exception
     */
    public function store(StoreCompany $request)
    {
        abort_if(!$this->user->cans('add_company'), 403);
        $data = $request->all();

        if ($request->hasFile('logo')) {
            $data['logo'] = Files::uploadLocalOrS3($request->logo, 'company-logo');
        }else{
            unset($data['logo']);
        }

        Company::create($data);

        return Reply::redirect(route('admin.company.index'), __('menu.companies') . ' ' . __('messages.createdSuccessfully'));
    }
    
    public function show($id){
        $this->company = Company::findOrFail($id);
    return view('admin.company.show', $this->data);
    }

    public function edit($id)
    {
        abort_if(!$this->user->cans('edit_company'), 403);

        $this->company = Company::find($id);
        return view('admin.company.edit', $this->data);
    }

    /**
     * @param StoreCompany $request
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function update(
        UpdateCompany  $request, $id)
    {
        abort_if(!$this->user->cans('edit_company'), 403);

        $data =  $request->all();

        $setting = Company::findOrFail($id);

        if ($request->hasFile('logo')) {
            $data['logo'] = Files::uploadLocalOrS3($request->logo, 'company-logo');
        }else{
            unset($data['logo']);
        }

        $setting->update($data);

        //update company's jobs status
        if($setting->status != $request->input('status')) {
            Job::where('company_id', $id)->update(['status' => $request->status]);
        }

        return Reply::redirect(route('admin.company.index'), __('menu.companies') . ' ' . __('messages.updatedSuccessfully'));
    }

    /**
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        abort_if(!$this->user->cans('delete_company'), 403);

        Company::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

    public function data()
    {
        abort_if(!$this->user->cans('view_company'), 403);

         $categories = Company::query();

          if (\request('filter_status') != "") {
            $categories->where('status', \request('filter_status'));
        }

        $categories->get();

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                $action .= '<a href="' . route('admin.company.show', [$row->id]) . '" class="btn btn-dark btn-circle"
                data-toggle="tooltip" onclick="this.blur()" data-original-title="' . __('app.view') . '"><i class="fa fa-search" aria-hidden="true"></i></a>&nbsp';

                if ($this->user->cans('edit_company')) {
                    $action .= '<a href="' . route('admin.company.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                      data-toggle="tooltip" onclick="this.blur()" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }

                if ($this->user->cans('delete_company')) {
                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" onclick="this.blur()" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }
                return $action;
            })
            ->editColumn('status', function ($row) {
                if($row->status == 'active'){
                    return '<label class="badge bg-success">'.__('app.active').'</label>';
                }
                if($row->status == 'inactive'){
                    return '<label class="badge bg-danger">'.__('app.inactive').'</label>';
                }
             })
            ->editColumn('logo', function ($row) {
                return '<img src="' . $row->logo_url . '" class="img-responsive" width = "150px"/>';
            })
            ->editColumn('company_name', function ($row) {
                return '<a href="' . route("admin.company.show", [$row->id]) . '">' . ucfirst($row->company_name) . '</a>';
                
            })
            ->addIndexColumn()
            ->rawColumns(['logo', 'action', 'status','company_name'])
            ->make(true);
    }
}

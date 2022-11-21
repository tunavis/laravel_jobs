<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\JobApplication;
use Illuminate\Http\Request;
use Froiden\Envato\Helpers\Reply;
use App\Http\Requests\StoreCurrency;
use Yajra\DataTables\Facades\DataTables;

class AdminCurrencyController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Currency Setting';
        $this->pageIcon = __('ti-settings');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->currencies = Currency::all();
        return view('admin.Currency.index', $this->data);
    }


    public function create()
    {
        return view('admin.Currency.create', $this->data);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCurrency $request)
    {
        $currency = new Currency();
        $currency->currency_symbol = $request->currency_symbol;
        $currency->currency_code = $request->currency_code;
        $currency->currency_name = $request->currency_name;
        $currency->save();

        return Reply::redirect(route('admin.currency-settings.index'), __('messages.createdSuccessfully'));
    }

    public function edit($id)
    {
        $this->currency = Currency::findOrFail($id);
        return view('admin.Currency.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCurrency $request, $id)
    {
        $currency = Currency::findOrFail($id);
        $currency->currency_symbol = $request->currency_symbol;
        $currency->currency_code = $request->currency_code;
        $currency->currency_name = $request->currency_name;
        $currency->save();

        return Reply::redirect(route('admin.currency-settings.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Currency::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }


}
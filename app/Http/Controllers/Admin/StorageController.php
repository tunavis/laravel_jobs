<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\StorageSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Requests\UpdateStorageSetting;

class StorageController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('menu.storageSetting');
        $this->pageIcon = 'ti-settings';
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $Data = StorageSetting::all();
        $this->local = $Data->filter(function ($value, $key) {
            return $value->filesystem == 'local' ;
        })->first();
        $this->S3data = $Data->filter(function ($value, $key) {
            return $value->filesystem == 'aws' ;
        })->first();


        if (!is_null($this->S3data)) {
            $authKeys = json_decode($this->S3data->auth_keys);
            $this->S3data->driver = $authKeys->driver;
            $this->S3data->key = $authKeys->key;
            $this->S3data->secret = $authKeys->secret;
            $this->S3data->region = $authKeys->region;
            $this->S3data->bucket = $authKeys->bucket;
        }
        return view('admin.storage-setting.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function store(UpdateStorageSetting $request) {
        if($request->storage == 'local'){

            $storage = StorageSetting::where('filesystem', 'local')->first();
            $storage->filesystem = $request->storage;
            $storage->status = 'enabled';
            $storage->save();
        }

        if($request->storage == 'aws'){
            
            $storage = StorageSetting::where('filesystem', 'aws')->first();

            if(is_null($storage)){
                $storage = new StorageSetting(); 
            }
              
            $storage->filesystem = $request->storage;
            $data = '{"driver": "s3", "key": "' . $request->aws_key . '", "secret": "' . $request->aws_secret . '", "region": "' . $request->aws_region . '", "bucket": "' . $request->aws_bucket . '"}';
            $storage->auth_keys = $data;
            $storage->status = 'enabled';
            $storage->save();
        }
     
        session()->forget('storage_setting');

        StorageSetting::where('filesystem', '!=' ,$request->storage)->update(['status' => 'disabled']);
        
        return Reply::success(__('messages.noteUpdateSuccess'));
    }
}

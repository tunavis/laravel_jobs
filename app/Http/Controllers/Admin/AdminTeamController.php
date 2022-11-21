<?php

namespace App\Http\Controllers\Admin;

use App\EmailSetting;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\StoreTeam;
use App\Http\Requests\UpdateTeam;
use App\Notifications\NewUser;
use App\Role;
use App\RoleUser;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Tests\HttpCache\StoreTest;
use Yajra\DataTables\Facades\DataTables;

class AdminTeamController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('menu.team');
        $this->pageIcon = 'icon-people';
        view()->share('calling_codes', $this->getCallingCodes());
    }

    public function index(){
        abort_if(! $this->user->cans('view_team'), 403);

        $this->users = User::all();
        return view('admin.team.index', $this->data);
    }

    public function create(){
        abort_if(! $this->user->cans('add_team'), 403);

        $this->roles = Role::all();
        $this->smtpSetting = EmailSetting::first();
        return view('admin.team.create', $this->data);
    }

    public function store(StoreTeam $request){
        abort_if(! $this->user->cans('add_team'), 403);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->calling_code = $request->calling_code;
        $user->mobile = $request->mobile;

        if ($request->hasFile('image')) {
            $user->image = Files::uploadLocalOrS3($request->image,'profile');
        }
        $user->save();


        //attach role
        $user->roles()->attach($request->role_id);

        //send notification
        $user->notify(new NewUser($request->password));

        return Reply::redirect(route('admin.team.index'), __('menu.team').' '.__('messages.createdSuccessfully'));
    }

    public function edit($id){
        abort_if(! $this->user->cans('edit_team'), 403);

        if($id == $this->user->id){
            abort(403);
        }

        $this->roles = Role::all();
        $this->team = User::find($id);
        return view('admin.team.edit', $this->data);
    }

    public function update(UpdateTeam $request, $id){
        abort_if(! $this->user->cans('edit_team'), 403);

        if($id == $this->user->id){
            abort(403);
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if($request->password != ''){
            $user->password = Hash::make($request->password);
        }

        if (($request->mobile != $user->mobile || $request->calling_code != $user->calling_code) && $user->mobile_verified == 1) {
            $user->mobile_verified = 0;
        }

        $user->mobile       = $request->mobile;
        $user->calling_code = $request->calling_code;

        if ($request->hasFile('image')) {
            $user->image = Files::uploadLocalOrS3($request->image,'profile');
        }else{
            Files::deleteFile($user->image, 'profile');
           $user->image = null;
       }
        $user->save();

        //attach role
        RoleUser::where('user_id', $id)->delete();
        $user->roles()->attach($request->role_id);

        return Reply::redirect(route('admin.team.index'), __('menu.team').' '.__('messages.updatedSuccessfully'));
    }


    public function data() {
        
        abort_if(! $this->user->cans('view_team'), 403);

        $users = User::all();
        $roles = Role::all();
        $admin = $users->filter(function($user){
            return $user->hasRole('admin');
        })->sortBy('created_at')->first();
         
        return DataTables::of($users)
            ->addColumn('action', function ($row) use($admin) {
                $action = '';

                //do not allow user to change own details
                if($row->id == $this->user->id){
                    return $action;
                }

                if($row->id != $admin->id && $this->user->cans('edit_team') ){
                    $action.= '<a href="' . route('admin.team.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                      data-toggle="tooltip" onclick="this.blur()" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }

                if($row->id != $admin->id && $this->user->cans('delete_team')){
                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" onclick="this.blur()" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }
                return $action;
            })
            ->addColumn('role_name', function ($row) use ($roles, $admin)  {

                //do not allow user to change own role
                if($row->id == $admin->id){
                    return $row->role->role->display_name;
                }
                if($row->id == $this->user->id){
                    return $row->role->role->display_name;
                }

                if( !$this->user->cans('edit_team')){
                    return $row->role->role->display_name;
                }
            
                $roleOption = '<select name="role_id" class="form-control role_id" data-row-id="'.$row->id.'">';
                foreach ($roles as $role){
                    $roleOption.= '<option ';
                    if( $row->id != $admin->id && $row->role->role->id == $role->id){

                        $roleOption.= ' selected ';
                    }

                    $roleOption.= 'value="'.$role->id.'">'.ucwords($role->display_name).'</option>';
                }
                $roleOption.= '</select>';
                return $roleOption;
            })
            ->editColumn('name', function ($row) {
                return '<div class="image-container"><div class="image"><img src='.$row->profile_image_url.' /></div>'.ucwords($row->name).'</div>';
            })
            ->editColumn('email', function ($row) {
                return $row->email;
            })
            ->rawColumns(['name', 'action', 'role_name'])
            ->addIndexColumn()
            ->make(true);
    }

    public function destroy($id)
    {
        abort_if(! $this->user->cans('delete_team'), 403);

        User::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

    public function changeRole(Request $request){
        //attach role
        $user = User::find($request->teamId);

        RoleUser::where('user_id', $request->teamId)->delete();
        $user->roles()->attach($request->roleId);

        return Reply::dataOnly(['status' => 'success']);
    }

}

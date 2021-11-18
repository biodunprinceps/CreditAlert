<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\services\CreateAdminService;
use App\Http\Requests\AdminAuthRequests\EditAdminRequest;
use App\Http\Requests\AdminAuthRequests\CreateAdminRequest;
use App\Http\Requests\AdminAuthRequests\ChangePasswordRequest;

class AdminAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['createAdmin','credentials']]);
    }

    public function createAdmin(CreateAdminRequest $request){
        $service = (new CreateAdminService($request->firstname,$request->lastname,$request->email,$request->pin));
        $service->createAdmin();
        if ($service->admin) {
            $token = auth()->guard('admin')->attempt(['email' => $request->get('email'), 'password' => md5($request->get('pin'))]);
            return response()->json(['status' => "success", "message" => "Admin Created Successfully", 'admin' => ['newAdmin' => $service->admin]], 200);
        } else {
            return response()->json(["message" => "An error occured, try again later", "status" => "error"], 400);
        }
    }

    protected function credentials(Request $request)
    {
        if (is_numeric($request->get('email'))) {
            $credentials = ['email' => $request->get('email'), 'password' => $request->get('pin')];
        } elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $request->get('email'), 'password' => $request->get('pin')];
        } else {
            return response()->json(["status" => "error", "message" => "Invalid Details"], 401);
        }
        if (!$token = auth()->guard('admin')->attempt($credentials)) {
            return response()->json(["status" => "error", "message" => "Invalid Details"], 401);
        }

        return $this->createNewToken($token);
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('admin')->factory()->getTTL(),
            'user' => auth()->guard('admin')->user(),
            'status' => 'success',
            "message" => "Successful login",
            "rawpin" => auth('admin')->user()->pin,
        ], 200);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $email = auth('admin')->user()->email;
        if (!$account = Admin::where('email', $email)->first()) {
            return response()->json(['status' => 'error', 'message' => 'The provided email was not found.'], 404);
        }
        if ($account->pin != $request->oldPin) {
            return response()->json(['status' => 'error', 'message' => 'The old password provided is incorrect.'], 400);
        }
        $newpin = $request->pin;
        $update_fields = array('password' => bcrypt($newpin));
        $account->update($update_fields);
        return response()->json(["status" => "success", "message" => "Password Successful Changed"], 200);
    }

    public function adminProfile()
    {
        $user = auth('admin')->user();
        $admin = Admin::where('authid',$user->authid)->first();
        return response()->json(['admin' => $admin, 'status' => 'success', 'message' => 'Successful'], 200);
    }

    public function editAdmin(EditAdminRequest $request)
    {
        $user = auth('admin')->user();
        $authid = $user->authid;
        if(!$admin = Admin::where('authid',$authid)->first()){
            return response()->json(['status' => "error", "message" => "Admin not found"], 400);
        }
        $field = [
            'email' => $request->email,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
        ];
        $admin->update($field);
        return response()->json(['status'=>'success','message'=>'Admin profile updated successfully'],200);
    }
}

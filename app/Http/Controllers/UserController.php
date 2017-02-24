<?php
/**
 * Created by PhpStorm.
 * User: plume
 * Date: 2017/2/13
 * Time: 17:20
 */
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller {
    public function __construct()
    {
        //
    }

    public function register(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::where('name', $username)->first();
        if(empty($username) || $user) {
            return ['code'=>1, 'msg'=>'invalid username'];
        }
        if(strlen($password)<8) {
            return ['code'=>2, 'msg'=>'invalid password'];
        }

        $new_user = new User();
        $new_user->name = $username;
        $new_user->nickname = $new_user->name;
        $new_user->avatar = "default.jpg";
        $new_user->encrypt = str_random('6');
        $new_user->password = md5(md5($password).$new_user->encrypt);

        $result = $new_user->save();
        if($result) {
            return ['code'=>0, 'msg'=>'success'];
        } else {
            return ['code'=>3, 'msg'=>'db error'];
        }
    }

    public function login(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');
        $user = User::where('name', $username)->first();
        if(empty($user)) {
            return ['code'=>1, 'msg'=>'error'];
        }
        if( $user->password != md5(md5($password).$user->encrypt) ) {
            return ['code'=>1, 'msg'=>'error'];
        }

        $user->last_login_time = date('Y-m-d H:i:s', time());
        $user->api_token = md5($user->name.$user->last_login_time);
        $user->api_token_expire_time = date('Y-m-d H:i:s', time()+12*3600);
        $user->save();
        setcookie("api_token", $user->api_token, time()+3600);
        return ['code'=>0, 'msg'=>'success'];
    }

    public function getUserProfile() {
        return ['code'=>0, 'data'=>Auth::user()];
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Encryption\DecryptException;

class UserController extends Controller
{

    public function index(Request $request)
    {
		perms_require(__METHOD__);
        $result = User::latest()->orderby('id', 'desc')->paginate();
        return view('users.index', compact('result'));
    }

    public function create(Request $request)
    {
		perms_require(__METHOD__);
		$roles = cache_roles();
        return view('users.new', compact('msg', 'roles'));
    }

    public function store(Request $request)
    {
		perms_require('user_create');
		if(!is_post())  return;
		extract($request->all());
		if (!$name || !$roles || !filter_var($email, FILTER_VALIDATE_EMAIL)){
			msg("表单填写错误");
		}

		$role_id = implode(",", $roles);

		$pdata = [
			"name" => $name,
			"email" => $email,
			"role_id" => $role_id,
			"password" =>  $password,
		];

        User::create($pdata);

		return redirect('users');

    }

    public function edit($id)
    {
		perms_require(__METHOD__);
		$roles = cache_roles();
        $rets = User::findOrFail($id);

        return view('users.edit', compact('rets', 'msg', 'roles'));
    }


    public function update(Request $request, $id)
    {
		perms_require('user_edit');
		if(!is_post())  return;
		extract($request->all());
		if (!$name || !$roles || !filter_var($email, FILTER_VALIDATE_EMAIL)){
			msg("表单填写错误");
		}

		$role_id = implode(",", $roles);

		$pdata = array(
			"name" => $name,
			"email" => $email,
			"role_id" => $role_id,
		);

		if($password) {
			$pdata['password'] = bcrypt($password);
		}

		User::whereId($id)->limit(1)->update($pdata);

		flash()->success('更新成功.');
		return redirect()->route('users.edit', $id);

    }

    public function destroy(Request $request, $ids)
    {
		perms_require(__METHOD__);
		if(!is_post())  return;

		if( $ids == "1" ) {
			msg("不能删除超级管理员");
		}

		User::whereId($ids)->limit(1)->delete();
		return redirect('users');
    }

	public function login(Request $request)
	{
		if(session('id')) {
			return redirect('users/info');
		}

		if (!$request->has('name') || !$request->has('password'))
		{
			return view('users.login');
		}

		extract($request->input());

        if (!Auth::attempt(['name' => $name, 'password' => $password])) {
			$msg = ["用户名与密码不匹配", "danger"];
			return view('users.login', compact('name', 'msg'));
        }

		set_sessinfo($user);
		return redirect('users/info');

	}

	public function info() {
//		$id = session('id');
//		$rets = \DB::table('users')->where("id", $id)->first();
		$rets = auth()->user();
        return view('users.info', compact('rets'));
	}

    public function resetpwd(Request $request)
    {
		$msg = $this->do_reset($request);
        return view('users.reset', compact('msg'));
    }

    public function do_reset(Request $request)
    {
		if(!is_post())  return;
		extract($request->all());

		if (!$password || !$old_password ){
			return ["表单填写错误", "danger"];
		}

		$user = auth()->user();
		if (!Auth::attempt(['name' => $user->name, 'password' => $old_password])) {
			return ["旧密码错误", "danger"];
		}

		$id = app('session')->get("id");

		$pdata = [
			"password" =>  bcrypt($password),
		];

		User::whereId($id)->limit(1)->update($pdata);

		$msg = "更新成功";
		return $msg;

    }

	public function logout(Request $request) {
		Auth::logout();
		app('session')->flush();
		return redirect('/login');
	}


    public function show(Request $request, $id)
    {

    }

}

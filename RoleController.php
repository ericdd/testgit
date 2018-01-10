<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{

	public function __construct(){
		$this->middleware(function ($request, $next) {
			roles_require('超级管理员');
			return $next($request);
		});
	}

    public function index(Request $request)
    {
		$result = Role::latest()->orderby('id', 'desc')->paginate(20);
        return view('roles.index', compact('result'));
    }

    public function create(Request $request)
    {
		$perms = cache_perms();
        return view('roles.new', compact('perms'));
    }

    public function store(Request $request)
    {
		if(!is_post())  return;
		extract($request->all());
		if (!$name || !$perms ){
			msg("表单填写错误");
		}

		$perm_id = implode(",", $perms);

		$pdata = [
			"name" => $name,
			"perm_id" => $perm_id,
		];

        Role::create($pdata);
		cache_roles(1);

		return redirect('roles');

    }

    public function edit($id)
    {
		$perms = cache_perms();
        $rets = Role::findOrFail($id);
        return view('roles.edit', compact('rets', 'msg', 'perms'));
    }

    public function update(Request $request, $id)
    {
		if(!is_post())  return;
		extract($request->all());

		if (!$name){
			msg("表单填写错误");
		}

		$perms and $perm_id = implode(",", $perms);

		$pdata = array(
			"name" => $name,
			"perm_id" => $perm_id,
		);

		Role::whereId($id)->limit(1)->update($pdata);
		cache_roles(1);

		flash()->success('更新成功.');
		return redirect()->route('roles.edit', $id);

    }

    public function destroy(Request $request, $ids)
    {
		if(!is_post())  return;

		if( $ids == "1" ) {
			msg("不能删除超级管理员角色");
		}

		Role::whereId($ids)->limit(1)->delete();
		cache_roles(1);

		return redirect('roles');

    }

    public function show(Request $request, $id)
    {

    }

}

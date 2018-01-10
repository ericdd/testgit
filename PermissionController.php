<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next)
			{
				roles_require('超级管理员');
				return $next($request);
			}
			);
	}

	public function index(Request $request)
	{
		$result = Permission::latest()->orderby('id', 'desc')->paginate(15);
		return view('permissions.index', compact('result'));
	}

	public function create(Request $request)
	{
		perms_require(__METHOD__);
		$perms = cache_perms();
		return view('permissions.new', compact('msg_danger', 'perms'));
	}

	public function store(Request $request)
	{
		if (!is_post()) return;

		extract($request->all());
		if (!$name)
		{
			msg("表单填写错误");
		}

		Permission::create($request->all());
		cache_perms(1);
		return redirect('permissions');
	}

	public function edit($id)
	{
		$rets = Permission::findOrFail($id);
		return view('permissions.edit', compact('rets', 'msg'));
	}

	public function update(Request $request, $id)
	{
		if (!is_post()) return;

		extract($request->all());

		if (!$name)
		{
			msg("表单填写错误");
		}

		Permission::whereId($id)->limit(1)->update($request->only("name"));
		cache_perms(1);

		flash()->success('更新成功.');
		return redirect()->route('permissions.edit', $id);
	}

	public function destroy(Request $request, $ids)
	{
		if (!is_post()) return;

		$aid = Permission::whereId($ids)->limit(1)->delete();
		cache_perms(1);

		return redirect('permissions');
	}

	public function show(Request $request, $id)
	{
	}
}

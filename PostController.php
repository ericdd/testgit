<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller {
	public function __construct() {
//        $this->middleware(function ($request, $next) {
		//            roles_require('Admin, 角色管理员');
		//            return $next($request);
		//        });
	}

	public function index(Request $request) {

		// $result = Post::latest()->with('user')->paginate();
		//$result = Post::with('user')->paginate();
		//$result = Post::paginate();

		$result = Post::orderby('id', 'desc')->with('user')->paginate(15);
		return view('posts.index', compact('result', 'msg'));
	}

	public function create(Request $request) {
		return view('posts.new');
	}

	public function store(Request $request) {
		if (!is_post()) {
			return;
		}

		extract($request->all());
		if (!$title || !$body) {
			msg("表单填写错误");
		}
		$user_id = app('session')->get("id");
		$request->request->add(['user_id' => $user_id]);

		Post::create($request->all());
		flash()->success('文章添加成功.');
		return redirect()->route('posts.index');
	}

	public function edit($id) {
		perms_require(__METHOD__);
		$rets = Post::findOrFail($id);
		return view('posts.edit', compact('rets'));
	}

	public function update(Request $request, $id) {
		if (!is_post()) {
			return;
		}

		if (!$request->input('title') || !$request->input('body')) {
			msg("表单填写错误");
		}

		$this->validate($request, [
			'title' => 'required|min:2',
			'body' => 'required|min:2',
		]);

		$post = Post::findOrFail($id);
		$post->title = $request->input('title');
		$post->body = $request->input('body');
		$post->save();

		flash()->success('Post has been updated.');
		return redirect()->route('posts.edit', $post->id);

	}

	public function show(Request $request, $id) {
		$post = Post::findOrFail($id);
		return view('posts.show', compact('post'));
	}

	public function destroy($ids) {
		perms_require(__METHOD__);
		if (!is_post()) {
			return;
		}

		$post = Post::findOrFail($ids);
		$post->delete();

		flash()->success('Post has been deleted.');

		//return redirect()->route('posts.index');
		return redirect("posts");

	}

}

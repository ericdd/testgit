<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Contracts\Encryption\DecryptException;

class TestController extends Controller
{
    public function index()
    {
		print_r($_GET);
//		$this->action();
 //   	return view(getThemeView('home.index'));

		$str = "$2y$10$JSwtMNRrZNbbzBtgiYxKOuN3bpdzhQySOY62q0krPWH1wEUsCudCS";
		echo decrypt($str),'<br />';

    }

    public function view()
    {
        $_info = 'other info';

        $_data  = [
            'city' => 'SZ',
            'age'  => 32,
        ];

        $_data1  = [
            'name' => 'Tom',
            'sex'  => 'female',
        ];

		file_put_contents('000.txt',  print_r($_data1, 1));

    	return view("view", $_data)->with('_data1',$_data1)->with('_info',$_info);
    }


	public function table() {
		$result = \DB::table('posts')->orderBy('id', 'desc')->paginate(15);
        return view('posts.bb', compact('result'));
	}

	public function info(Request $request)
	{
		echo __METHOD__,'<br />';
		echo get_class(), '<br />';
		echo __CLASS__, '<br />';
		echo __FUNCTION__, '<br />';

		echo $_SERVER['REQUEST_METHOD'],'<br />';

		echo '----------------------------------------<br />';

		echo $request->ajax(),'<br />';		// 等同 $request->fullUrl(),'<br />';
		echo $request->method(),'<br />';
		echo $request->root(),'<br />';
		echo $request->url(),'<br />';
		echo $request->fullUrl(),'<br />';
		echo $request->path(),'<br />';
		echo $request->ip(),'<br />';


		echo '----------------------------------------<br />';


		//return view('test');
	}


	public function conf() {

		echo config('app.fallback_locale'), '<br />';
		echo config('any.pagination'), '<br />';

		printr(session()->all());

		printr(config());

	}

	public function sess3() {

		echo session("ra"),'+++++<br />';
		echo session("ra", rand()),'+++<br />';
		echo session("locale", "not setting lang"),'+++<br />';

		echo app('session')->get("ra", "0000"), '<br />';

		dd(session()->all());

	}

	public function sess(){

		Session::put('ID', rand(1,100));
		Session::put('USERNAME', '徐文志');
		Session::put('ra', rand(1000,9009));

		session(array(
			'z-key' => 123,
			'z-nn' => "eric",
		));


		/**
		使用push方法创建Session数组
		每次刷新后数组user都会追加
		*/

		Session::push('user.id', 1);
		Session::push('user.name', 'azxuwen');
		echo 'Session Created!!';

	}

	public function sess1() {

		flash()->success('更新成功.'.rand());    // flash_notification

		//使用get方法获取session变量或session数组

		echo app('session')->get("zname", "0000"), '<br />';
		echo app('session')->get("dddd", "gggg"), '<br />';

		if(Session::has("flashmsg")) {
			echo Session::get("flashmsg"), '<br />';
		} else {
			Session::flash("flashmsg", "更新成功".rand());
		}
		printr(app('session')->all());
	}

	/**
	文件头要加上use Route; 或者使用\Route::has
	命名路由让你可以更方便的为特定路由生成 URL 或进行重定向。
	Route::has 返回的是 Route::get('route','Admin\TestController@route')->name('a1');
	*/
	public function route() {

		var_dump(\Route::has('a1'));
		var_dump(\Route::has('r.a1'));
		var_dump(\Route::has('login'));
		var_dump(\Route::has('route'));
		var_dump(\Route::has('/'));

	}

	/**
	http://blog.csdn.net/zls986992484/article/details/52824962
	*/
	public function select() {

		$user1 = \DB::select('select * from users where id = ?', [1]);
		$user2 = \DB::select('select * from users where id > :id', [':id'=>1]);

		printr($user1);

		printr($user2);

		$rr = \DB::table("users")->where('id','<=',2)->get();
		printr($rr);


		$user4 = \DB::table("users")->first();  //结果集第一条记录
		printr($user4);

	}

	public function update() {

		$name = "nolan".rand(100,999);

		$aid1 = \DB::table('posts')->where('id', 6)->update(['title' => $name]);
		$aid2 = \DB::update("update posts set title = ? where id = ? limit 1", ["aaa".rand(), 1] );
		$aid3 = \DB::table('posts')->whereIn("id", [4,5])->update(['title' => $name]);


		echo $aid1,'<br />';
		echo $aid2,'<br />';
		echo $aid3,'<br />';

	}

	public function pluck() {

		$rets = \DB::table('roles')->pluck('name');
		printr($rets);
		printr($rets->implode(', '));

		$ret1 = \DB::table('posts')->whereId(1)->get();

		$ret2 = \DB::table('posts')->whereIn('id', [4,5])->pluck("title", "id");

		printr($ret1);
		printr($ret2);

		printr($rets2->all());


		foreach($ret2 as $k => $v) {
			echo $k , ',', $v,'<br />';

		}

	}

	public function auth() {
		$user = auth()->user();
		echo $user->id,'<br />';
		echo $user->name,'<br />';

		$ret = \Auth::check();
		var_dump($ret);
		echo '<br />';

		$ret = \Auth::user();
		echo $ret->id,'<br />';
		echo $ret->name,'<br />';


	}



}

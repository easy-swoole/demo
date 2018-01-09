迁移pagination分页
---

> 仓库地址: [pagination](https://github.com/illuminate/pagination)

安装
------

```
composer require illuminate/pagination
```

我们可以用illuminate/pagination分页了
```
 $users = User::paginate(15);


//在你的模板

{!! $users->links() !!}

```
然后你将看到一堆莫名其妙的错误,没关系，让我们来解决它。既然不能像laravel那样使用，我们只好使用自定义分页

Model
```
//Model.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Core\Http\Request;

class Model extends EloquentModel
{
    public function scopePage($query ,$pageSize){
        $page = Request::getInstance()->getRequestParam('page') ? Request::getInstance()->getRequestParam('page') : 1;
        $paginator = $query->paginate($pageSize, ['*'],'page',$page);
        $paginator->setPath(\Core\Http\Request::getInstance()->getServerParams()['request_uri']);
        return $paginator;
    }
}


//User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;

class User extends Model
{

}

```
控制器
```
// 在Index控制器类添加以下方法
function index(){
    
        $user = User::page(5);
        //or
        //$user = User::where('name', 'like', "%{$keyword}%")->page(5);
        //$user->appends('keyword',$keyword);
}
```
自定义函数(在Blade提到过)
```
if (! function_exists('paginator')){
    /**
     * 自定义分页
     * @param string $paginator data
     * @return string
     */
    function paginator($paginator = ''){
        $request = \Core\Http\Request::getInstance();
        $page = $request->getRequestParam('page') ?: 1;
        $win = new \Illuminate\Pagination\UrlWindow($paginator);
        $url_arr = $win->get(3);
        $text = '<ul class="pagination">';
        if ($paginator->hasPages()) { //有结果集才显示啊
            if (!$paginator->onFirstPage()) {
                $text.="<li><a href=\"{$paginator->previousPageUrl()}\" rel=\"prev\" class=\"page-numbers previous\">上一页</a></li>";
            }

            if (isset( $url_arr['first'] )) {
                foreach ($url_arr['first'] as $k=> $v ) {
                    if ($k == $paginator->currentPage()) {
                        $style = "<span class=\"page-numbers current\">$k</span>";
                    }else{
                        $style = "<a href=\"{$v}\" class=\"page-numbers\">$k</a>";
                    }
                    $text.= "<li>$style</li>";
                }
            }

            if (isset( $url_arr['slider'] )) {

                foreach ($url_arr['slider'] as $k=> $v ) {
                    if ($k == $paginator->currentPage()) {
                        $style = "<span class=\"page-numbers current\">$k</span>";
                    }else{
                        $style = "<a href=\"{$v}\" class=\"page-numbers\">$k</a>";
                    }
                    $text.= "<li>$style</li>";
                }
            }else{
                if ($url_arr['last'])
                $text.= "<li class=\"disabled\"><span class=\"page-numbers\">...</span></li>";
            }

            if (isset( $url_arr['last'] )) {
                foreach ($url_arr['last'] as $k=> $v ) {
                    if ($k == $paginator->currentPage()) {
                        $style = "<span class=\"page-numbers current\">$k</span>";
                    }else{
                        $style = "<a href=\"{$v}\" class=\"page-numbers\">$k</a>";
                    }
                    $text.= "<li>$style</li>";
                }
            }

            if ($paginator->lastPage()!=$page) {
                $text.="<li><a href=\"{$paginator->nextPageUrl()}\" rel=\"prev\" class=\"page-numbers next\">下一页</a></li>";
            }

        }
        $text .= '</ul>';
        return $text;
    }
}
```
模板
```
//在你的模板

{!! paginator($products) !!}
```
然后，分页大功告成!
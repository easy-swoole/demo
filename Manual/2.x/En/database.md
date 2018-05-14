# DataBase ORM
EasySwoole support use custom ORM。

## Example 
composer require
```bash
composer require joshcam/mysqli-database-class:dev-master
```
singleton usage
```php
namespace App\Utility;


use EasySwoole\Config;
use EasySwoole\Core\AbstractInterface\Singleton;

class Mysql
{
    use Singleton;

    private $db;

    function __construct()
    {
        $conf = Config::getInstance()->getConf('MYSQL');
        $this->db =  new \MysqliDb($conf['HOST'],$conf['USER'],$conf['PASSWORD'],$conf['DB_NAME']);
    }

    public function getConnect():\MysqliDb
    {
        return $this->db;
    }

    public static function getLimit($page = 1,$page_num = 10){
        if($page >= 1){
            $limit = Array(($page-1)*$page_num,$page_num);
        }else{
            $limit = Array(0,$page_num);
        }
        return $limit;
    }
}
```

## Coroutine MysqliDb ORM

locate in namespace EasySwoole\Core\Swoole\Coroutine\Client
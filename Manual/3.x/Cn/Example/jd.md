# 爬虫案例（[京东苹果手机为例子](https://github.com/HeKunTong/easyswoole3_demo)）

## 采集京东苹果手机任务，也就是链接

```php
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-10-10
 * Time: 下午1:49
 */

namespace App\Task;


use App\Queue\Queue;
use EasySwoole\Curl\Request;

class Jd
{

    public function run()
    {
        // TODO: Implement run() method.
        $url = 'https://list.jd.com/list.html';
        $params = [
            'cat' => '9987,653,655',
            'ev' => 'exbrand_14026',
            'sort' => 'sort_rank_asc',
            'trans' => 1,
            'JL' => '3_品牌_Apple'
        ];
        $url = $url.'?'.http_build_query($params);
        echo $url.PHP_EOL;
        $request = new Request($url);
        $request->setUserOpt([CURLOPT_REFERER => 'https://list.jd.com/list.html?cat=9987,653,655']);
        $body = $request->exec()->getBody();
        $html = new \simple_html_dom();
        $html->load($body);
        $curr = $html->find('.p-num a.curr', 0);
        $skip = $html->find('.p-skip b', 0);
        if (!empty($curr) && !empty($skip)) {
            $currentPage = 'https://list.jd.com'.$curr->href;
            $total = intval($skip->plaintext);
            $i = 2;
            echo $currentPage.PHP_EOL;
            $queue = new Queue();
            $queue->lPush($currentPage);
            while($i <= $total) {
                $page = str_replace('page=1', "page=$i", $currentPage);
                echo $page.PHP_EOL;
                $queue->lPush($page);
                $i++;
            }
        }
    }
}
```

## 开启两个协程任务,处理采集任务

```php
for($i = 1; $i <= 2; $i++) {
    $queue = new Queue();
    \Co::create(function () use (&$timer, $queue){
        $goodTask = new JdGood();
        $task = $queue->rPop();
        if($task) {
            echo 'task-----'.$task.PHP_EOL;
            $goodTask->handle($task);
        } else {
            echo 'end-----'.PHP_EOL;
            if ($timer) {
                Timer::clear($timer);
            }
        }
        unset($goodTask);
    });
    unset($queue);
}
```

## 采集任务处理逻辑

```php
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-10-10
 * Time: 下午3:33
 */

namespace App\Task;


use App\Model\Jd\JdBean;
use App\Model\Jd\JdModel;
use App\Utility\Pool\MysqlPool;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\Curl\Request;

class JdGood
{
    protected $db;

    function __construct()
    {
        $db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj();
        if ($db) {
            $this->db = $db;
        } else {
            throw new \Exception('mysql pool is empty');
        }
    }

    function handle($url)
    {
        $request = new Request($url);
        $body = $request->exec()->getBody();
        $html = new \simple_html_dom();
        $html->load($body);
        $list = $html->find('ul.gl-warp', 0);
        $len = count($list->find('.gl-item'));
        $skus = [];
        for ($i = 0; $i < $len; $i++) {
            $item = $list->find('.gl-item', $i);
            $sku = $item->find('.j-sku-item', 0)->getAttribute('data-sku');
            $skus[] = 'J_'.$sku;
            $name = trim($item->find('.p-name em', 0)->plaintext);
            $shop = $item->find('.p-shop', 0)->getAttribute('data-shop_name');
            $data = [
                'name' => $name,
                'shop' => $shop,
                'sku' => $sku,
            ];
            $bean = new JdBean($data);
            $model = new JdModel($this->db);
            $model->insert($bean);
        }
        $this->getPrice($skus);
    }

    private function getPrice($skus)
    {
        $url = 'https://p.3.cn/prices/mgets';
        $params = [
            'skuIds' => implode(',', $skus)
        ];
        $url = $url.'?'.http_build_query($params);
        $request = new Request($url);

        $result = json_decode($request->exec()->getBody(), true);

        foreach ($result as $item) {
            $sku = substr($item['id'], 2);
            $price = floatval($item['p']) * 100;
            $bean = new JdBean();
            $bean->setSku($sku);
            $model = new JdModel($this->db);
            $model->update($bean, $price);
        }
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($this->db);
    }
}    
``` 

采集任务分两步。

1. 采集手机名，sku以及店铺
2. 采集手机价格

## 数据表结构

```sql
CREATE TABLE `jd` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) DEFAULT NULL,
  `shop` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=806 DEFAULT CHARSET=utf8mb4;
```
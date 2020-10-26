<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\WebSocketController;


class Index extends Base
{
    public function index()
    {
        $this->response()->setMessage('this is index');
    }
}
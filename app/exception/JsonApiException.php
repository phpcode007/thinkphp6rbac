<?php
namespace app\exception;

use Psr\Log\AbstractLogger;
use think\exception;

class JsonApiException extends exception
{
    public $code = 0;
    public $msg = '';
    public $data = '';

    public function __construct($code='',$msg='',$data='')
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
    }

    public function getError()
    {
        if (empty($this->data)) {
            return ['code'=>$this->code, 'msg' => $this->msg];
        } else {
            return ['code'=>$this->code, 'msg' => $this->msg, 'data'=>$this->data];
        }
    }


}
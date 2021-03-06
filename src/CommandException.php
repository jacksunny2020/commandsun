<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jacksunny\CommandSun;

use Jacksunny\CommandSun\ActionResultContract;

/**
 * 命令异常对象，各个命令执行时抛出的异常必须是命令异常类型
 *
 * @author 施朝阳
 * @date 2017-7-6 14:01:12
 */
class CommandException extends \Exception {

    protected $res;
    
    public function getResult(){
        return $this->res;
    }

    public function __construct(ActionResultContract $res, string $message = "", int $code = 0, \Throwable $previous = null) {
        $this->res = $res;
        parent::__construct($message, $code, $previous);
    }

}

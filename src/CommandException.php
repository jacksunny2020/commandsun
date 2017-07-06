<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jacksunny\CommandSun;

/**
 * Description of CommandException
 *
 * @author 施朝阳
 * @date 2017-7-6 14:01:12
 */
class CommandException extends \Exception {
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

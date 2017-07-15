<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jacksunny\CommandSun;

use Jacksunny\CommandSun\BaseActionResult;
use Closure;
use Jacksunny\CommandSun\CommandException;
use Jacksunny\CommandSun\CommandMonocase;
use Jacksunny\AuthInfo\LoginInfoContract;
use Illuminate\Support\Facades\Log;
use Jacksunny\CommandSun\CommandFalseContract;

/**
 * Description of CmdUserLoginNamePasswordLog
 *
 * @author 施朝阳
 * @date 2017-7-6 15:22:36
 */
class CommandFalse extends CommandMonocase implements CommandFalseContract {

    public function __construct() {
        parent::__construct("False");
    }

    protected function doAction(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null) {
        if (!isset($res)) {
            $res = new BaseActionResult(false);
        }
//        $res->setSuccess(false);
//        return $res;
        $result = clone $res;
        $result->setSuccess(false);
        return $result;
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jacksunny\CommandSun;

use Jacksunny\CommandSun\BaseActionResult;
use Closure;

/**
 *
 * @author 施朝阳
 * @date 2017-7-6 14:37:18
 */
interface CommandContract {

    function getCommandCheckInput(): CommandContract;

    function setCommandCheckInput(CommandContract $command_check_input = null): CommandContract;

    function getCommandCheckOutput(): CommandContract;

    function setCommandCheckOutput(CommandContract $command_check_output = null): CommandContract;

    function getCommandDoAction(): CommandContract;

    function setCommandDoAction(CommandContract $command_do_action = null): CommandContract;

    function getCommandPostSuccessAction(): CommandContract;

    function setCommandPostSuccessAction(CommandContract $command_post_success_action = null): CommandContract;

    function getCommandPostFailAction(): CommandContract;

    function setCommandPostFailAction(CommandContract $command_post_fail_action = null): CommandContract;

    function addCommand(CommandContract $command = null): CommandContract;

    function removeCommand(CommandContract $command = null): CommandContract;

    function countCommand(): int;

    function tryExec(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null): BaseActionResult;

    function exec(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null): BaseActionResult;
}

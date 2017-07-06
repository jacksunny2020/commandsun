<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jacksunny\CommandSun;

use Jacksunny\CommandSun\CommandException;
use Jacksunny\CommandSun\BaseActionResult;
use Closure;
use Jacksunny\CommandSun\CommandContract;

/**
 * Description of Command
 *
 * @author 施朝阳
 * @date 2017-7-6 13:59:29
 */
class BaseCommand implements CommandContract {

    protected $command_check_input;
    protected $command_check_output;
    protected $command_do_action;
    protected $command_post_success_action;
    protected $command_post_fail_action;

    public function __construct(CommandContract $command_check_input = null, CommandContract $command_check_output = null, CommandContract $command_do_action = null, CommandContract $command_post_success_action = null, CommandContract $command_post_fail_action = null) {
//        $this->command_check_input = $command_check_input;
//        $this->command_check_output = $command_check_output;
//        $this->command_do_action = $command_do_action;
//        $this->command_post_success_action = $command_post_success_action;
//        $this->command_post_fail_action = $command_post_fail_action;

        $this->setCommandCheckInput($command_check_input)
                ->setCommandCheckOutput($command_check_output)
                ->setCommandDoAction($command_do_action)
                ->setCommandPostSuccessAction($command_post_success_action)
                ->setCommandPostFailAction($command_post_fail_action);
    }

    public function getCommandCheckInput(): CommandContract {
        return $this->command_check_input;
    }

    public function setCommandCheckInput(CommandContract $command_check_input = null): CommandContract {
        $this->command_check_input = $command_check_input;
        return $this;
    }

    public function getCommandCheckOutput(): CommandContract {
        return $this->command_check_output;
    }

    public function setCommandCheckOutput(CommandContract $command_check_output = null): CommandContract {
        $this->command_check_output = $command_check_output;
        return $this;
    }

    public function getCommandDoAction(): CommandContract {
        return $this->command_do_action;
    }

    public function setCommandDoAction(CommandContract $command_do_action = null): CommandContract {
        $this->command_do_action = $command_do_action;
        return $this;
    }

    public function getCommandPostSuccessAction(): CommandContract {
        return $this->command_post_success_action;
    }

    public function setCommandPostSuccessAction(CommandContract $command_post_success_action = null): CommandContract {
        $this->command_post_success_action = $command_post_success_action;
        return $this;
    }

    public function getCommandPostFailAction(): CommandContract {
        return $this->command_post_fail_action;
    }

    public function setCommandPostFailAction(CommandContract $command_post_fail_action = null): CommandContract {
        $this->command_post_fail_action = $command_post_fail_action;
        return $this;
    }

    public function tryExec(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null): BaseActionResult {
        try {
            $res = $this->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        } catch (CommandException $ex) {
            $res = new BaseActionResult(false, $data, $ex->getCode(), $ex->getMessage(), $data, $context);
        }
        return $res;
    }

    public function exec(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null): BaseActionResult {
        if (!isset($res)) {
            $res = new BaseActionResult(true, $data, null, null);
            $res->setOriginalData($data);
            $res->setOriginalContext($context);
        }
        if (isset($trans_input)) {
            $data = $trans_input($res, $data, $context);
        }
        $res = $this->checkThrowInput($res, $data, $context);
        $res = $this->doAction($res, $data, $context);
        if ($res->getSuccess()) {
            $res = $this->postSuccessAction($res, $data, $context);
            if (isset($succ_closure)) {
                $succ_closure($res, $data, $context);
            }
        } else {
            $res = $this->postFailAction($res, $data, $context);
            if (isset($fail_closure)) {
                $fail_closure($res, $data, $context);
            }
        }
        $res = $this->checkThrowOutput($res, $data, $context);
        if (isset($trans_output)) {
            $res = $trans_output($res, $data, $context);
        }
        return $res;
    }

    protected function checkThrowInput(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null) {
        if (isset($this->command_check_input)) {
            $res = $this->command_check_input->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        }
        return $res;
    }

    protected function checkThrowOutput(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null) {
        if (isset($this->command_check_output)) {
            $res = $this->command_check_output->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        }
        return $res;
    }

    protected function doAction(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null) {
        if (isset($this->command_do_action)) {
            $res = $this->command_do_action->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        }
        return $res;
    }

    protected function postSuccessAction(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null) {
        if (isset($this->command_post_success_action)) {
            $res = $this->command_post_success_action->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        }
        return $res;
    }

    protected function postFailAction(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null) {
        if (isset($this->command_post_fail_action)) {
            $res = $this->command_post_fail_action->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        }
        return $res;
    }

    public function addCommand(CommandContract $command = null): CommandContract {
        return $this;
    }

    public function countCommand(): int {
        return 1;
    }

    public function removeCommand(CommandContract $command = null): CommandContract {
        return $this;
    }

}

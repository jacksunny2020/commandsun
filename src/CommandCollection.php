<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jacksunny\CommandSun;

use Jacksunny\CommandSun\CommandContract;
use Closure;

/**
 * Description of CommandCollection
 *
 * @author 施朝阳
 * @date 2017-7-6 14:42:52
 */
class CommandCollection implements CommandContract {

    protected $commands;

    public function __construct(array $commands) {
        $this->commands = $commands;
    }

    public function exec(BaseActionResult $res = null, $data = null, $context = null, \Closure $succ_closure = null, \Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null): BaseActionResult {
        if (!isset($res)) {
            $res = new BaseActionResult(true, $data, null, null);
        }
        if (isset($this->commands) && count($this->commands) > 0) {
            foreach ($this->commands as $command) {
                $res = $command->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
            }
        }
        return $res;
    }

    public function getCommandCheckInput(): CommandContract {
        return $this;
    }

    public function getCommandCheckOutput(): CommandContract {
        return $this;
    }

    public function getCommandDoAction(): CommandContract {
        return $this;
    }

    public function getCommandPostFailAction(): CommandContract {
        return $this;
    }

    public function getCommandPostSuccessAction(): CommandContract {
        return $this;
    }

    public function setCommandCheckInput(CommandContract $command_check_input = null): CommandContract {
        return $this;
    }

    public function setCommandCheckOutput(CommandContract $command_check_output = null): CommandContract {
        return $this;
    }

    public function setCommandDoAction(CommandContract $command_do_action = null): CommandContract {
        return $this;
    }

    public function setCommandPostFailAction(CommandContract $command_post_fail_action = null): CommandContract {
        return $this;
    }

    public function setCommandPostSuccessAction(CommandContract $command_post_success_action = null): CommandContract {
        return $this;
    }

    public function tryExec(BaseActionResult $res = null, $data = null, $context = null, \Closure $succ_closure = null, \Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null): BaseActionResult {
        try {
            $res = $this->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        } catch (CommandException $ex) {
            $res = new BaseActionResult(false, $data, $ex->getCode(), $ex->getMessage(), $data, $context);
        }
        return $res;
    }

    public function addCommand(CommandContract $command = null): CommandContract {
        if (!isset($command)) {
            return $this;
        }
        if (!isset($this->commands)) {
            $this->commands = array($command);
        } else {
            $this->commands[] = $command;
        }
        return $this;
    }

    public function countCommand(): int {
        if (!isset($this->commands)) {
            return 0;
        } else {
            return count($this->commands);
        }
    }

    public function removeCommand(CommandContract $command = null): CommandContract {
        if (!isset($command)) {
            return $this;
        }
        if (!isset($this->commands)) {
            return $this;
        } else {
            $key = array_search($command, $this->commands);
            //从数组中移除某项
            array_splice($this->commands, $key, 1);
        }
        return $this;
    }

}

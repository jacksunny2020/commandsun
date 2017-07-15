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
 * 简单命令集合对象
 *
 * @author 施朝阳
 * @date 2017-7-6 14:42:52
 */
class CommandCollection implements CommandContract {

    /**
     * 用于保存命令对象的集合
     */
    protected $commands;

    /**
     * 当前命令包含于的命令对象，称之为父命令对象
     * 父命令对象的children中包含了该对象
     */
    protected $parent;

    public function __construct(array $commands) {
        $this->commands = $commands;
    }

    /**
     * 对于命令集合对象的执行，其实是分别执行命令集合中的每个命令对象
     */
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

    /**
     * 命令集合对象的执行，如果抛出异常会被捕获并设置错误消息到结果对象res中
     */
    public function tryExec(BaseActionResult $res = null, $data = null, $context = null, \Closure $succ_closure = null, \Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null): BaseActionResult {
        try {
            $res = $this->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        } catch (CommandException $ex) {
            $temp_res = $ex->getResult();
            if (!isset($temp_res)) {
                $res = new BaseActionResult(false, $data, $ex->getCode(), $ex->getMessage(), $data, $context);
            } else {
                $res = $temp_res;
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

    public function getCommandPostActionFinal(): CommandContract {
        return $this;
    }

    public function setCommandPostActionFinal(CommandContract $command_post_action_final = null): CommandContract {
        return $this;
    }

    /**
     * 添加指定命令$command到当前命令集合中
     */
    public function addCommand(CommandContract $command = null, CommandContract $next_command = null, $command_connector = null): CommandContract {
        if (!isset($command)) {
            return $this;
        }
        if (!isset($this->commands)) {
            $this->commands = array($command);
        } else {
            $this->commands[] = $command;
        }
        $command->setParent($this);
        return $this;
    }

    /**
     * 当前命令集合对象中命令对象数量
     */
    public function countCommand(): int {
        if (!isset($this->commands)) {
            return 0;
        } else {
            return count($this->commands);
        }
    }

    /**
     * 从当前命令集合中移除指定的命令对象$command
     */
    public function removeCommand(CommandContract $command = null, CommandContract $next_command = null, $command_connector = null): CommandContract {
        if (!isset($command)) {
            return $this;
        }
        if (!isset($this->commands)) {
            return $this;
        } else {
            $command->setParent(null);
            $key = array_search($command, $this->commands);
            //从数组中移除某项
            array_splice($this->commands, $key, 1);
        }
        return $this;
    }

    /**
     * 清空当前命令集合中所有命令对象
     */
    public function clearCommand(): CommandContract {
        if (isset($this->commands)) {
            $this->commands = array();
        }
        return $this;
    }

    /**
     * 根据当前命令集合中所有的命令对象的名称通过->连接作为命令集合命令对象的名称
     */
    public function getCommandName(): string {
        $result = "";

        //以设置的命令对象的名称优先，如果没有设置就采用命令集合中包含的命令的命令名称来组装出命令集合对象的名称
        if (isset($this->name)) {
            return $this->name;
        }
        if (isset($this->commands)) {
            $result = "";
            foreach ($this->commands as $command) {
                if ($result === "") {
                    $result = $command->getCommandName();
                } else {
                    $result = $result . "->" . $command->getCommandName();
                }
            }
            $result = $result . "";
        }

        return $result;
    }

    /**
     * 设置命令集合对象的名称，这里设置的名称优先使用
     */
    public function setCommandName(string $name): CommandContract {
        $this->name = "" . $name . "";
        return $this;
    }

    /**
     * 为该命令集合对象生成对应的跟踪信息
     */
    public function trace(ActionResultContract $res, $message, $data = null) {
        $level = $this->getLevel();
        $level_prefix_str = str_repeat("*", $level);
        if (isset($data)) {
            $res->addTrace($level_prefix_str . $this->getCommandName() . $message . json_encode(is_array($data) ? $data : array($data)));
        } else {
            $res->addTrace($level_prefix_str . $this->getCommandName() . $message);
        }
    }

    public function getParent() {
        return $this->parent;
    }

    public function setParent(CommandContract $parent): CommandContract {
        $this->parent = $parent;
        if (isset($this->commands)) {
            foreach ($this->commands as $command) {
                $command->setParent($parent);
            }
        }
        return $this;
    }

    public function getChildern(): array {
        return $this->commands;
    }

    public function getLevel() {
        $parent = $this->getParent();
        if (!isset($parent)) {
            return 5;
        } else {
            return $parent->getLevel() + 3;
        }
    }

}

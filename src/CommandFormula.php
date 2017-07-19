<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jacksunny\CommandSun;

use Jacksunny\CommandSun\CommandContract;
use Closure;
use Jacksunny\CommandSun\CommandTrueContract;
use Jacksunny\CommandSun\CommandFalseContract;

//命令连接词符号定义
//define('CMD_CONNECTOR_NOT', "NOT");
//define('CMD_CONNECTOR_AND', "AND");
//define('CMD_CONNECTOR_OR', "OR");
define('CMD_CONNECTOR_NOT', "【!】");
define('CMD_CONNECTOR_AND', "【&】");
define('CMD_CONNECTOR_OR', "【|】");

/**
 * 命令公式对象
 *
 * @author 施朝阳
 * @date 2017-7-6 14:42:52
 */
class CommandFormula implements CommandContract {

    /**
     * 用于保存公式中的一个命令对象
     */
    protected $command;

    /**
     * 用于保存公式中的另一个命令对象
     */
    protected $command_next;

    /**
     * 命令公式之间的连接器，比如AND,NOT,OR
     */
    protected $command_connector;

    /**
     * 是否抛出异常当当前公式运算结果为false时
     * 有时候不需要抛出异常，比如某个命题公式取反运算时导致false一般不需要抛出异常，除非是需要中断执行流程的结果为false的命题公式需要抛出异常，一般都不抛出默认false
     */
    protected $throw_when_false = false;

    /**
     * 当前命令包含于的命令对象，称之为父命令对象
     * 父命令对象的children中包含了该对象
     */
    protected $parent;

    public function __construct(CommandContract $command, CommandContract $command_next = null, $command_connector = null, $throw_when_false = false) {
        $this->command = $command;
        if (isset($this->command)) {
            $this->command->setParent($this);
        }
        $this->command_next = $command_next;
        if (isset($this->command_next)) {
            $this->command_next->setParent($this);
        }
        $this->command_connector = $command_connector;
        $this->throw_when_false = $throw_when_false;
    }

    /**
     * 对于命令集合对象的执行，其实是分别执行命令集合中的每个命令对象
     */
    public function exec(BaseActionResult $res = null, $data = null, $context = null, \Closure $succ_closure = null, \Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null): BaseActionResult {
        if (!isset($res)) {
            $res = new BaseActionResult(true, $data, null, null);
        }
        if ($this instanceof CommandTrue) {
            $res->setSuccess(true);
            return $res;
        }
        if ($this instanceof CommandFalse) {
            $res->setSuccess(false);
            return $res;
        }
        $res_temp = new BaseActionResult(true);
        //$this->trace($res, "(dddd" . $res_temp->getSuccess() . ")", $res);
        if (isset($this->command)) {
            //有命令和连接词，则可能是NOT A或者A OR B之类的公式
            if (isset($this->command_connector)) {
                if (isset($this->command_next)) {
                    //可能是 A OR B之类公式
                    $this->trace($res, "(开始执行第一个命令" . $this->command->getCommandName() . ")", $res);
                    $res_command = $this->command->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
                    //$this->trace($res, "(结束执行命令" . $this->command->getCommandName() . ".【执行结果】)" . $res_command->getSuccess(), $res);
                    $this->trace($res, "(结束执行第一个命令" . $this->command->getCommandName() . ".【执行结果】)" . $res_command->getSuccess());
                    $this->trace($res, "(开始执行另一个命令" . $this->command_next->getCommandName() . ")", $res);
                    $res_command_next = $this->command_next->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
                    //$this->trace($res, "(结束执行另一个命令" . $this->command_next->getCommandName() . ".【执行结果】)" . $res_command_next->getSuccess(), $res);
                    $this->trace($res, "(结束执行另一个命令" . $this->command_next->getCommandName() . ".【执行结果】)" . $res_command_next->getSuccess());
                    $this->trace($res, "(开始二元命令公式" . $this->getCommandName() . "运算)", $res);
                    switch ($this->command_connector) {
                        case CMD_CONNECTOR_AND:
                            $res_temp->setSuccess($res_command->applyAnd($res_command_next)->getSuccess());
                            break;
                        case CMD_CONNECTOR_OR:
                            $res_temp->setSuccess($res_command->applyOr($res_command_next)->getSuccess());
                            break;
                    }
                    //$this->trace($res, "(结束二元命令公式" . $this->getCommandName() . "运算【逻辑结果】" . $res->getSuccess(), $res);
                    $this->trace($res, "(结束二元命令公式" . $this->getCommandName() . "运算【逻辑结果】" . $res_temp->getSuccess());
                } else {
                    //可能是NOT A之类公式
                    $this->trace($res, "(开始执行第一个命令" . $this->command->getCommandName() . ")", $res);
                    $res_command = $this->command->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
                    //$this->trace($res, "(结束执行命令" . $this->command->getCommandName() . "【执行结果】)" . $res_command->getSuccess(), $res);
                    $this->trace($res, "(结束执行第一个命令" . $this->command->getCommandName() . "【执行结果】)" . $res_command->getSuccess());
                    $this->trace($res, "(开始一元命令公式" . $this->getCommandName() . "运算)", $res);
                    //$this->trace($res, "(test" . $res_temp->getSuccess() . ")", $res);
                    switch ($this->command_connector) {
                        case CMD_CONNECTOR_NOT:
                            $res_temp->setSuccess($res_command->applyNot()->getSuccess());
                            break;
                    }
                    //$this->trace($res, "(testeq" . ($res_temp->getSuccess() == false) . ")", $res);
                    $this->trace($res, "(结束一元命令公式" . $this->getCommandName() . "运算)【逻辑结果】" . $res_temp->getSuccess(), $res);
                }
            } else {
                //没有连接词，就是普通命令作为公式
                $res = $this->command->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
                $res_temp = $res;
            }
        }
        $this->trace($res, "命令公式" . $this->getCommandName() . "【最终逻辑结果】", $res_temp->getSuccess());
        if (isset($res) && !$res->getSuccess() && $this->throw_when_false) {
            $res->setSuccess($res_temp->getSuccess());
            $this->trace($res, $this->getCommandName(), $res);
            throw new CommandException($res);
        }
        $res->setSuccess($res->getSuccess() && $res_temp->getSuccess());
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
        $this->command = $command;
        $this->command_next = $next_command;
        $this->command_connector = $command_connector;
        $command->setParent($this);
        $next_command->setParent($this);
        return $this;
    }

    /**
     * 当前命令集合对象中命令对象数量
     */
    public function countCommand(): int {
        return 1;
    }

    /**
     * 从当前命令集合中移除指定的命令对象$command
     */
    public function removeCommand(CommandContract $command = null, CommandContract $next_command = null, $command_connector = null): CommandContract {
        if (!isset($command)) {
            return $this;
        }

        if (isset($this->command)) {
            $this->command->setParent(null);
        }
        if (isset($this->command_next)) {
            $this->command_next->setParent(null);
        }
        $this->command_connector = null;

        return $this;
    }

    /**
     * 清空当前命令集合中所有命令对象
     */
    public function clearCommand(): CommandContract {
        $this->command = null;
        $this->command_next = null;
        $this->command_connector = null;
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
        if (isset($this->command)) {
            $result = "";
            if (isset($this->command_connector)) {
                if (isset($this->command_next)) {
                    $result = "{" . $this->command->getCommandName() . "" . $this->command_connector . "" . $this->command_next->getCommandName() . "}";
                } else {
                    $result = "{" . $this->command_connector . "" . $this->command->getCommandName() . "}";
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
            //$res->addTrace($level_prefix_str . $this->getCommandName() . $message . json_encode(is_array($data) ? $data : array($data)));
            $res->addTrace($level_prefix_str . $this->getCommandName() . $message);
        } else {
            $res->addTrace($level_prefix_str . $this->getCommandName() . $message);
        }
    }

    public function getParent() {
        return $this->parent;
    }

    public function setParent(CommandContract $parent): CommandContract {
        $this->parent = $parent;
        if (isset($this->command)) {
            $this->command->setParent($parent);
        }
        if (isset($this->command_next)) {
            $this->command->setParent($parent);
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

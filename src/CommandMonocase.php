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
 * 命令单体即简单命令
 *
 * @author 施朝阳
 * @date 2017-7-6 13:59:29
 */
class CommandMonocase implements CommandContract {

    /**
     * 包含的用于检查输入数据的子命令对象
     */
    protected $command_check_input;

    /**
     * 包含的用于检查输出数据的子命令对象
     */
    protected $command_check_output;

    /**
     * 包含的用于执行核心动作的子命令对象
     */
    protected $command_do_action;

    /**
     * 包含的用于执行核心动作成功后需要执行的子命令对象
     */
    protected $command_post_success_action;

    /**
     * 包含的用于执行核心动作失败后需要执行的子命令对象
     */
    protected $command_post_fail_action;
    
    /**
     * 包含的用于执行核心动作后输出前需要执行的子命令对象
     */
    protected $command_post_action_final;

    /**
     * 当前命令包含于的父命令对象
     */
    protected $parent;

    /**
     * 命令名称
     */
    protected $name;

    
    
    public function __construct(string $name = null, CommandContract $command_check_input = null, CommandContract $command_check_output = null, CommandContract $command_do_action = null, CommandContract $command_post_success_action = null, CommandContract $command_post_fail_action = null,$command_post_action_final = null) {
        //如果未指定命令对象的名称时自动生成一个，格式为cmd+类名称
        if (!isset($name)) {
            $name = "cmd" . get_class();
        }
        $this->setCommandName($name);
        $this->setCommandCheckInput($command_check_input)
                ->setCommandCheckOutput($command_check_output)
                ->setCommandDoAction($command_do_action)
                ->setCommandPostSuccessAction($command_post_success_action)
                ->setCommandPostFailAction($command_post_fail_action)
                ->setCommandPostActionFinal($command_post_action_final)
                ;
    }

    /**
     * 输入数据验证命令对象或命令集合
     */
    public function getCommandCheckInput(): CommandContract {
        return $this->command_check_input;
    }

    public function setCommandCheckInput(CommandContract $command_check_input = null): CommandContract {
        if (isset($command_check_input)) {
            $command_check_input->setParent($this);
        }
        $this->command_check_input = $command_check_input;
        return $this;
    }

    /**
     * 输出数据验证命令对象或命令集合
     */
    public function getCommandCheckOutput(): CommandContract {
        return $this->command_check_output;
    }

    public function setCommandCheckOutput(CommandContract $command_check_output = null): CommandContract {
        if (isset($command_check_output)) {
            $command_check_output->setParent($this);
        }
        $this->command_check_output = $command_check_output;
        return $this;
    }

    /**
     * 主命令对象或命令集合
     */
    public function getCommandDoAction(): CommandContract {
        return $this->command_do_action;
    }

    public function setCommandDoAction(CommandContract $command_do_action = null): CommandContract {
        if (isset($command_do_action)) {
            $command_do_action->setParent($this);
        }
        $this->command_do_action = $command_do_action;
        return $this;
    }

    /**
     * 主命令执行成功后需要执行的命令对象或命令集合
     */
    public function getCommandPostSuccessAction(): CommandContract {
        return $this->command_post_success_action;
    }

    public function setCommandPostSuccessAction(CommandContract $command_post_success_action = null): CommandContract {
        if (isset($command_post_success_action)) {
            $command_post_success_action->setParent($this);
        }
        $this->command_post_success_action = $command_post_success_action;
        return $this;
    }

    /**
     * 主命令执行失败后需要执行的命令对象或命令集合
     */
    public function getCommandPostFailAction(): CommandContract {
        return $this->command_post_fail_action;
    }

    public function setCommandPostFailAction(CommandContract $command_post_fail_action = null): CommandContract {
        if (isset($command_post_fail_action)) {
            $command_post_fail_action->setParent($this);
        }
        $this->command_post_fail_action = $command_post_fail_action;
        return $this;
    }
    
    /**
     * 主命令执行后输出前需要执行的命令对象或命令集合
     */
    public function getCommandPostActionFinal(): CommandContract {
        return $this->command_post_action_final;
    }

    public function setCommandPostActionFinal(CommandContract $command_post_action_final = null): CommandContract {
        if (isset($command_post_action_final)) {
            $command_post_action_final->setParent($this);
        }
        $this->command_post_action_final = $command_post_action_final;
        return $this;
    }

    /**
     * 执行核心动作，如果抛出异常会捕获后记录到结果对象res中并返回给调用者
     */
    public function tryExec(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null): BaseActionResult {
        try {
            $res = $this->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        } catch (CommandException $ex) {
            $temp_res = $ex->getResult();
            if (!isset($temp_res)) {
                $res = new BaseActionResult(false, $data, $ex->getCode(), $ex->getMessage(), $data, $context);
            }else{
                $res = $temp_res;
            }
            $this->trace($res, $ex->getMessage());
        }
        return $res;
    }

    /**
     * 执行核心动作，如果抛出异常不处理，需要由调用者处理异常CommandException
     * 依次会执行输入开始转换回调、输入数据检查、核心动作、核心动作成功或失败后动作、输出数据检查，输出数据转换回调
     */
    public function exec(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null): BaseActionResult {
        if (!isset($res)) {
            $res = new BaseActionResult(true, $data, null, null);
            $res->setOriginalData($data);
            $res->setOriginalContext($context);
        }
        if (isset($trans_input)) {
            $this->trace($res, "(输入开始转换)", $data);
            $data = $trans_input($res, $data, $context);
        }
        $this->trace($res, "(输入开始验证)", $data);
        $res = $this->checkThrowInput($res, $data, $context);
        $this->trace($res, "(核心动作开始)", $data);
        $res = $this->doAction($res, $data, $context);
        if ($res->getSuccess()) {
            $this->trace($res, "(核心动作成功)");
            $this->trace($res, "(开始成功后动作)");
            $res = $this->postSuccessAction($res, $data, $context);
            if (isset($succ_closure)) {
                $this->trace($res, "(开始成功回调)");
                $succ_closure($res, $data, $context);
            }
        } else {
            $this->trace($res, "(核心动作失败)");
            $this->trace($res, "(开始失败后动作)");
            $res = $this->postFailAction($res, $data, $context);
            if (isset($fail_closure)) {
                $this->trace($res, "(开始失败回调)");
                $fail_closure($res, $data, $context);
            }
        }
        
        $this->trace($res, "(核心动作后输出前开始验证)", $data);
        $res = $this->postActionFinal($res, $data, $context);
        $this->trace($res, "(核心动作后输出前开始验证)", $data);
        
        $this->trace($res, "(输出开始验证)", $res);
        $res = $this->checkThrowOutput($res, $data, $context);
        if (isset($trans_output)) {
            $this->trace($res, "(输出开始转换)", $res);
            $res = $trans_output($res, $data, $context);
        }
        return $res;
    }

    /**
     * 执行输入数据检查子命令
     */
    protected function checkThrowInput(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null) {
        if (isset($this->command_check_input)) {
            $res = $this->command_check_input->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        }
        return $res;
    }

    /**
     * 执行输出数据检查子命令
     */
    protected function checkThrowOutput(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null) {
        if (isset($this->command_check_output)) {
            $res = $this->command_check_output->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        }
        return $res;
    }

    /**
     * 执行核心动作的子命令
     */
    protected function doAction(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null) {
        if (isset($this->command_do_action)) {
            $res = $this->command_do_action->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        }
        return $res;
    }

    /**
     * 执行核心动作执行成功后的动作对应的子命令
     */
    protected function postSuccessAction(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null) {
        if (isset($this->command_post_success_action)) {
            $res = $this->command_post_success_action->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        }
        return $res;
    }

    /**
     * 执行核心动作执行失败后的动作对应的子命令
     */
    protected function postFailAction(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null) {
        if (isset($this->command_post_fail_action)) {
            $res = $this->command_post_fail_action->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        }
        return $res;
    }
    
    
    /**
     * 执行核心动作执行后输出前的动作对应的子命令
     */
    protected function postActionFinal(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null) {
        if (isset($this->command_post_action_final)) {
            $res = $this->command_post_action_final->exec($res, $data, $context, $succ_closure, $fail_closure, $trans_input, $trans_output);
        }
        return $res;
    }
    

    /**
     * 添加指定命令作为子命令到当前命令对象的子命令网络中
     */
    public function addCommand(CommandContract $command = null, CommandContract $next_command = null, $command_connector = null): CommandContract {
        return $this;
    }

    /**
     * 子命令数量
     */
    public function countCommand(): int {
        return 0;
    }

    /**
     * 将指定的命令从子命令网络中移除
     */
    public function removeCommand(CommandContract $command = null, CommandContract $next_command = null, $command_connector = null): CommandContract {
        return $this;
    }

    /**
     * 清空所有子命令
     */
    public function clearCommand(): CommandContract {
        return $this;
    }

    /**
     * 命令名称
     */
    public function getCommandName(): string {
        if (!isset($this->name)) {
            $this->name = "" . "cmd" . get_class($this) . "";
            if (isset($this->command_do_action)) {
                $this->name = $this->name . "." . $this->command_do_action->getCommandName();
            }
            $this->name = $this->name . "";
        }
        return $this->name;
    }

    public function setCommandName(string $name): CommandContract {
        $this->name = "" . $name . "";
        return $this;
    }

    /**
     * 在指定结果$res中添加跟踪信息message和相关数据data，一般结合命令层次用于显示负责命令的执行过程
     */
    public function trace(ActionResultContract $res, $message, $data = null) {
        $level = $this->getLevel();
        $level_prefix_str = str_repeat("*", $level);
        if (isset($data)) {
            if (is_array($data)) {
                $res->addTrace($level_prefix_str . $this->getCommandName() . $message . json_encode($data));
            } else {
                $res->addTrace($level_prefix_str . $this->getCommandName() . $message . json_encode($data));
            }
        } else {
            $res->addTrace($level_prefix_str . $this->getCommandName() . $message);
        }
    }

    public function getParent() {
        return $this->parent;
    }

    public function setParent(CommandContract $parent): CommandContract {
        if (isset($parent)) {
            $this->parent = $parent;
//            $this->setCommandName($parent->getCommandName() . "." . $this->name);
        } else {
//            if (isset($this->parent)) {
//                $old_name = $this->getCommandName();
//                $new_name = str_replace($this->parent->getCommandName(), "", $old_name);
//                $this->setCommandName($new_name);
//            }
            $this->parent = $parent;
        }
        if (isset($this->parent)) {
            $this->setCommandName($this->parent->getCommandName() . "." . $this->name);
        }
        return $this;
    }

    public function getChildern(): array {
        return null;
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

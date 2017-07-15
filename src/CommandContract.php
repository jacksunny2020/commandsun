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
 * 命令对象接口
 * @author 施朝阳
 * @date 2017-7-6 14:37:18
 */
interface CommandContract {

    /**
     * 命令名称 
     */
    function getCommandName(): string;

    function setCommandName(string $name): CommandContract;

    //管理基本组成部分
    /**
     * 输入数据验证命令对象或命令集合
     */
    function getCommandCheckInput(): CommandContract;

    function setCommandCheckInput(CommandContract $command_check_input = null): CommandContract;

    /**
     * 输出数据验证命令对象或命令集合
     */
    function getCommandCheckOutput(): CommandContract;

    function setCommandCheckOutput(CommandContract $command_check_output = null): CommandContract;

    /**
     * 主命令对象或命令集合
     */
    function getCommandDoAction(): CommandContract;

    function setCommandDoAction(CommandContract $command_do_action = null): CommandContract;

    /**
     * 主命令执行成功后需要执行的命令对象或命令集合
     */
    function getCommandPostSuccessAction(): CommandContract;

    function setCommandPostSuccessAction(CommandContract $command_post_success_action = null): CommandContract;

    /**
     * 主命令执行失败后需要执行的命令对象或命令集合
     */
    function getCommandPostFailAction(): CommandContract;

    function setCommandPostFailAction(CommandContract $command_post_fail_action = null): CommandContract;
    
    
     /**
     * 主命令执行后输出前需要执行的命令对象或命令集合
     */
    function getCommandPostActionFinal(): CommandContract;

    function setCommandPostActionFinal(CommandContract $command_post_action_final = null): CommandContract;

    //父子关系
    /**
     * 包含该命令的命令对象
     */
    function setParent(CommandContract $parent): CommandContract;

    function getParent();

    /**
     * 包含的命令对象列表
     */
    function getChildern(): array;

    /**
     * 当前命令在包含关系的层次
     */
    function getLevel();

    /**
     * 添加指定命令作为子命令到当前命令对象的子命令网络中
     * 子命令可以是简单命令如$command,也可以复杂的命令组合比如 $command $command_connector $next_command比如 $command AND $next_command
     */
    function addCommand(CommandContract $command = null, CommandContract $next_command = null, $command_connector = null): CommandContract;

    /**
     * 将指定的命令从子命令网络中移除
     * 子命令可以是简单的命令或复杂命令组合 
     */
    function removeCommand(CommandContract $command = null, CommandContract $next_command = null, $command_connector = null): CommandContract;

    /**
     * 清空所有子命令
     */
    function clearCommand(): CommandContract;

    /**
     * 子命令数量
     */
    function countCommand(): int;

    //执行命令
    /**
     * 执行命令，如果命令或子命令抛出异常会自动捕获异常并将错误消息返回在res中
     */
    function tryExec(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null): BaseActionResult;

    /**
     * 执行命令，如果有异常会直接抛出需要调用者自己处理异常
     */
    function exec(BaseActionResult $res = null, $data = null, $context = null, Closure $succ_closure = null, Closure $fail_closure = null, Closure $trans_input = null, Closure $trans_output = null): BaseActionResult;

    /**
     * 在指定结果$res中添加跟踪信息message和相关数据data，一般结合命令层次用于显示负责命令的执行过程
     */
    function trace(ActionResultContract $res, $message, $data = null);
}

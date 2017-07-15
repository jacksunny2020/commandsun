<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jacksunny\CommandSun;

/**
 *
 * @author 施朝阳
 * @date 2017-7-5 17:39:41
 */
interface ActionResultContract {
    function getSuccess(): bool;

    function setSuccess($success): ActionResultContract;

    function getCode(): string;

    function setCode($code): ActionResultContract;

    function getMessage(): string;

    function setMessage($message): ActionResultContract;

    function getData();

    function setData($data): ActionResultContract;

    function getOriginalData();

    function setOriginalData($data): ActionResultContract;

    function getOriginalContext();

    function setOriginalContext($context): ActionResultContract;

    function getTraceArray(): array;

    function setTraceArray(array $traces): ActionResultContract;

    function addTrace($trace): ActionResultContract;

    function clearTraces(): ActionResultContract;

    function applyNot():ActionResultContract;

    function applyAnd(ActionResultContract $action_result):ActionResultContract;

    function applyOr(ActionResultContract $action_result):ActionResultContract;
}

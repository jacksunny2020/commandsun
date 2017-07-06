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

    function setSuccess($success);

    function getCode(): string;

    function setCode($code);

    function getMessage(): string;

    function setMessage($message);

    function getData();

    function setData($data);
    
    function getOriginalData();

    function setOriginalData($data);
    
    function getOriginalContext();

    function setOriginalContext($context);
}

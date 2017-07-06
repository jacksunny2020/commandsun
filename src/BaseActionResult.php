<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jacksunny\CommandSun;

/**
 * Description of BaseActionResult
 *
 * @author 施朝阳
 * @date 2017-7-5 17:41:38
 */
class BaseActionResult implements ActionResultContract {

    protected $code;
    protected $data;
    protected $message;
    protected $success;
    protected $original_data;
    protected $original_context;

    public function __construct($success, $data = null, $code = null, $message = null, $original_data = null, $original_context = null) {
        $this->success = $success;
        $this->data = $data;
        $this->code = $code;
        $this->message = $message;
        $this->original_data = $original_data;
        $this->original_context = $original_context;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function getData() {
        return $this->data;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getSuccess(): bool {
        return $this->success;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setSuccess($success) {
        $this->success = $success;
    }

    public function getOriginalContext() {
        return $this->original_context;
    }

    public function getOriginalData() {
        return $this->original_data;
    }

    public function setOriginalContext($context) {
        $this->original_context = $context;
    }

    public function setOriginalData($data) {
        $this->original_data = $data;
    }

}

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
    protected $traces;

    public function __construct($success, $data = null, $code = null, $message = null, $original_data = null, $original_context = null, $traces = null) {
        $this->success = $success;
        $this->data = $data;
        $this->code = $code;
        $this->message = $message;
        $this->original_data = $original_data;
        $this->original_context = $original_context;
        $this->traces = $traces;
    }

    public function getCode(): string {
        return $this->code ?? "";
    }

    public function getData() {
        return $this->data;
    }

    public function getMessage(): string {
        return $this->message ?? "";
    }

    public function getSuccess(): bool {
        return $this->success;
    }

    public function setCode($code): ActionResultContract {
        $this->code = $code;
        return $this;
    }

    public function setData($data): ActionResultContract {
        $this->data = $data;
        return $this;
    }

    public function setMessage($message): ActionResultContract {
        $this->message = $message;
        return $this;
    }

    public function setSuccess($success): ActionResultContract {
        $this->success = $success;
        return $this;
    }

    public function getOriginalContext() {
        return $this->original_context;
    }

    public function getOriginalData() {
        return $this->original_data;
    }

    public function setOriginalContext($context): ActionResultContract {
        $this->original_context = $context;
        return $this;
    }

    public function setOriginalData($data): ActionResultContract {
        $this->original_data = $data;
        return $this;
    }

    public function getTraceArray(): array {
        return $this->traces;
    }

    public function setTraceArray(array $traces): ActionResultContract {
        $this->traces = $traces;
        return $this;
    }

    public function addTrace($trace): ActionResultContract {
        if (isset($trace)) {
            $this->traces[] = "" . $trace;
        }
        return $this;
    }

    public function clearTraces(): ActionResultContract {
        if (isset($this->traces)) {
            $this->traces = array();
        }
        return $this;
    }

    public function __toString(): string {
        return 'code:' . $this->code . "," . 'data:' . json_encode($this->data) . "," . 'message:' . $this->message . "," . 'success:' . $this->success . "," . 'org_data:' . json_encode($this->original_data) . "," . 'org_context:' . json_encode($this->original_context);
    }

    public function applyAnd(ActionResultContract $action_result): ActionResultContract {
        if (!isset($action_result)) {
            return $this;
        }
        $this->success = $this->success && $action_result->getSuccess();
        return $this;
    }

    public function applyNot(): ActionResultContract {
        $this->success = !$this->success;
        return $this;
    }

    public function applyOr(ActionResultContract $action_result): ActionResultContract {
        if (!isset($action_result)) {
            return $this;
        }
        $this->success = $this->success || $action_result->getSuccess();
        return $this;
    }

}

<?php

namespace Alimi7372\DBLogger\Entities;

use Alimi7372\DBLogger\Enums\LogLevel;
use Alimi7372\DBLogger\Enums\LogType;
use Alimi7372\DBLogger\Enums\ServiceType;
use Alimi7372\DBLogger\Models\Log;

class LogBase
{
    protected LogObject $logObject;
    protected Log $logged;

    public function __construct()
    {
        $this->logObject = new LogObject();
    }

    public function setLevel(LogLevel $level): LogBase
    {
        $this->logObject->setLogLevel($level);
        return $this;
    }

    public function setType(LogType $type): LogBase
    {
        $this->logObject->setLogType($type);
        return $this;
    }

    public function setContext(mixed $context, bool $isJSON = false): LogBase
    {
        $this->json($context, $isJSON);
        $this->logObject->setContext($context);
        return $this;
    }


    public function setService(ServiceType $type): LogBase
    {
        $this->logObject->setService($type);
        return $this;
    }

    public function setUser(string|null $user): LogBase
    {
        $this->logObject->setUser($user);
        return $this;
    }

    public function setMessage(mixed $message): LogBase
    {
        $this->logObject->setMessage($message);
        return $this;
    }

    public function setInput(mixed $input, bool $isJSON = false): LogBase
    {
        $this->json($input, $isJSON);
        $this->logObject->setInput($input);
        return $this;
    }

    public function setOutPut(mixed $output, bool $isJSON = false): LogBase
    {
        $this->json($output, $isJSON);
        $this->logObject->setOutput($output);
        return $this;
    }

    public function setExtra(mixed $extra_data, bool $isJSON = false): LogBase
    {
        $this->json($extra_data, $isJSON);
        $this->logObject->setExtraData($extra_data);
        return $this;
    }

    public function setStatusCode(string $statusCode): LogBase
    {
        $this->logObject->setStatusCode($statusCode);
        return $this;
    }

    public function setResponseTime(float $response_time): LogBase
    {
        $this->logObject->setResponseTime($response_time);
        return $this;
    }

    public function getLogged(): Log
    {
        return $this->logged;
    }

    protected function setLogged($log): LogBase
    {
        $this->logged = $log;
        return $this;
    }

    public function setURI($uri): LogBase
    {
        $this->logObject->setURI($uri);
        return $this;
    }

    private function json(mixed &$context, bool $isJSON): void
    {
        $context = ($isJSON) ? json_encode($context) : $context;
    }
}


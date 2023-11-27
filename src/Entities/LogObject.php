<?php

namespace Alimi7372\DBLogger\Entities;

use Alimi7372\DBLogger\Enums\LogLevel;
use Alimi7372\DBLogger\Enums\LogType;
use Alimi7372\DBLogger\Enums\ServiceType;

class LogObject
{
    private string $logType;
    private string $logLevel;
    private string $message;
    private string $service;
    private string|null $user;
    private string $input;
    private null|int $input_id = null;
    private null|int $output_id = null;
    private null|int $extra_data_id = null;
    private null|int $context_id = null;
    private string $output;
    private string $extra_data;
    private string $context;
    private string $status_code;
    private string $response_time;
    private string $uri;

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message ?? '';
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return ['context' => $this->context ?? ''];
    }

    /**
     * @param string $context
     */
    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    /**
     * @param LogLevel $logLevel
     */
    public function setLogLevel(LogLevel $logLevel): void
    {
        $this->logLevel = $logLevel->name;
    }

    /**
     * @return string
     */
    public function getLogType(): string
    {
        return $this->logType;
    }

    /**
     * @param LogType $logType
     */
    public function setLogType(LogType $logType): void
    {
        $this->logType = $logType->name;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user ?? '';
    }

    /**
     * @param string|null $user
     */
    public function setUser(string|null $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * @param ServiceType $service
     */
    public function setService(ServiceType $service): void
    {
        $this->service = $service->name;
    }

    /**
     * @return array
     */
    public function getInput(): array
    {
        return ['context' => $this->input ?? ''];
    }

    /**
     * @param string $input
     */
    public function setInput(string $input): void
    {
        $this->input = $input;
    }

    /**
     * @return array
     */
    public function getOutput(): array
    {
        return ['context' => $this->output ?? ''];
    }

    /**
     * @param string $output
     */
    public function setOutput(string $output): void
    {
        $this->output = $output;
    }

    /**
     * @return array
     */
    public function getExtraData(): array
    {
        return ['context' => $this->extra_data ?? ''];
    }

    /**
     * @param string $extra_data
     */
    public function setExtraData(string $extra_data): void
    {
        $this->extra_data = $extra_data;
    }

    /**
     * @return int|null
     */
    public function getInputId(): int|null
    {
        return $this->input_id;
    }

    /**
     * @param int $id
     */
    public function setInputId(int $id): void
    {
        $this->input_id = $id;
    }

    /**
     * @return int|null
     */
    public function getOutputId(): int|null
    {
        return $this->output_id;
    }

    /**
     * @param int $id
     */
    public function setOutputId(int $id): void
    {
        $this->output_id = $id;
    }

    /**
     * @return int|null
     */
    public function getContextId(): int|null
    {
        return $this->context_id;
    }

    /**
     * @param int $id
     */
    public function setContextId(int $id): void
    {
        $this->context_id = $id;
    }

    /**
     * @return int|null
     */
    public function getExtraDataId(): int|null
    {
        return $this->extra_data_id;
    }

    /**
     * @param int $id
     */
    public function setExtraDataId(int $id): void
    {
        $this->extra_data_id = $id;
    }

    /**
     * @return string
     */
    public function getStatusCode(): string
    {
        return $this->status_code;
    }

    /**
     * @param string $code
     */
    public function setStatusCode(string $code): void
    {
        $this->status_code = $code;
    }

    /**
     * @return string
     */
    public function getURI(): string
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setURI(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @return float
     */
    public function getResponseTime(): float
    {
        return $this->response_time;
    }

    /**
     * @param float $response_time
     */
    public function setResponseTime(float $response_time): void
    {
        $this->response_time = $response_time;
    }

    public function toArray()
    {
        return [
            'level' => $this->getLogLevel(),
            'type' => $this->getLogType(),
            'service' => $this->getService(),
            'status_code' => $this->getStatusCode(),
            'message' => $this->getMessage(),
            'input_id' => $this->getInputId(),
            'output_id' => $this->getOutputId(),
            'context_id' => $this->getContextId(),
            'extra_data_id' => $this->getExtraDataId(),
            'uri' => $this->getURI(),
            'user' => $this->getUser(),
            'response_time' => $this->getResponseTime(),
        ];
    }

}

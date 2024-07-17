<?php

namespace Alimi7372\DBLogger;

use Alimi7372\DBLogger\Entities\LogBase;
use Alimi7372\DBLogger\Enums\LogType;
use Alimi7372\DBLogger\Models\Log;
use Alimi7372\DBLogger\Models\LogContext;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class DBLogger extends LogBase
{
    private bool $enable;
    public function __construct(LogType $type)
    {
        parent::__construct();
        $this->setType($type);
        $this->enable = config('dblogger.enable');
    }

    public function log(): Log|null
    {
        $logged = null;
        if ($this->enable) {
            if ($this->logObject->getInput()['context'] != '') {
                $input= LogContext::create($this->logObject->getInput());
                $this->logObject->setInputId($input->id);

            }
            if ($this->logObject->getOutput()['context'] != '') {
                $output= LogContext::create($this->logObject->getOutput());
                $this->logObject->setOutputId($output->id);

            }
            if ($this->logObject->getExtraData()['context'] != '') {
                $extraData=LogContext::create($this->logObject->getExtraData());
                $this->logObject->setExtraDataId($extraData->id);

            }
            if ($this->logObject->getContext()['context'] != '') {
                $context= LogContext::create($this->logObject->getContext());
                $this->logObject->setContextId($context->id);
            }
            $logged = Log::create($this->logObject->toArray());
            $this->setLogged($logged);
        }
        return $logged;
    }

    public function exception($exception): DBLogger
    {
        switch (get_class($exception)) {
            case ConnectException::class:
                $this->connectionException($exception);
                break;
            case RequestException::class:
            case ClientException::class:
                $this->requestException($exception);
                break;
            default:
                $this->defaultException($exception);
                break;
        }
        return $this;
    }

    protected function requestException(RequestException $ex): DBLogger
    {
        $ex = $ex->getResponse();
//        $this->setOutPut((string)$ex->getBody());
        $msg = $ex ? (string)$ex->getBody() : $ex->getMessage();
        $this->setMessage($msg);
        $this->setStatusCode($ex->getStatusCode());
        return $this;
    }

    protected function connectionException(ConnectException $ex): DBLogger
    {
        $this->setMessage($ex->getMessage());
//        $this->setOutPut($ex->getMessage());
        $this->setStatusCode(408);
        return $this;
    }

    protected function defaultException(Exception $ex): DBLogger
    {
        $this->setOutPut($ex->getMessage());
        $this->setStatusCode(500);
        return $this;
    }

}

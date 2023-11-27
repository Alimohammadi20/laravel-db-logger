<?php

namespace Alimi7372\DBLogger\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'level' => $this->level,
            'uri' => $this->uri,
            'type' => $this->type,
            'service' => $this->service,
            'response_time' => $this->response_time,
            'status_code' => $this->status_code,
            'message' => $this->message,
            'user' => $this->user,
            'input' => $this->input ? $this->input->context : null,
            'output' => $this->output ? $this->output->context : null,
            'context' => $this->context ? $this->context->context : null,
            'extra_data' => $this->extraData ? $this->extraData->context : null,
            'created_at' => jdate($this->created_at)->format('Y-m-d H:i'),
        ];
    }
}

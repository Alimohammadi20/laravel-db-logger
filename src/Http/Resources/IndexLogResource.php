<?php

namespace Alimi7372\DBLogger\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexLogResource extends JsonResource
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
            'type' => $this->type,
            'level' => $this->level,
            'message' => $this->message,
            'user' => $this->user,
            'uri' => $this->uri,
            'method' => $this->method,
            'response_time' => $this->response_time,
            'created_at' => jdate($this->created_at)->format('Y-m-d H:i'),
        ];
    }
}

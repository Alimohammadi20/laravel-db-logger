<?php

namespace Alimi7372\DBLogger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class LogContext extends Model
{
    protected $table = 'log_contexts';
    protected $guarded = ['id'];

    protected function context(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value) {
                $data = is_array($value) ? $value : json_decode($value, true);

                if (isset($data['bytes'])) {
                    $data['bytes'] = 'File Uploaded';
                }

                return json_encode($data);
            }
        );
    }
}

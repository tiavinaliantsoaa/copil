<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use HasUuids;

    protected $fillable = [
        'communication_report_id', 'category', 'original_name', 'path',
        'mime_type', 'size', 'uploaded_by',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function report(): BelongsTo
    {
        return $this->belongsTo(CommunicationReport::class, 'communication_report_id');
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}

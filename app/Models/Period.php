<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Period extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'label', 'starts_on', 'status', 'closed_at', 'closed_by'];

    protected $casts = ['starts_on' => 'date', 'closed_at' => 'datetime'];

    public function report(): HasOne
    {
        return $this->hasOne(CommunicationReport::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}

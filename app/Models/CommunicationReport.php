<?php

namespace App\Models;

use App\Services\ReportBlueprint;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunicationReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id', 'owner', 'due_date', 'status', 'summary', 'channels',
        'graphics', 'training', 'tools', 'events', 'projects', 'action_plan', 'updated_by',
    ];

    protected $casts = [
        'due_date' => 'date', 'summary' => 'array', 'channels' => 'array',
        'graphics' => 'array', 'training' => 'array', 'tools' => 'array',
        'events' => 'array', 'projects' => 'array', 'action_plan' => 'array',
    ];

    protected static function booted(): void
    {
        static::retrieved(function (self $report): void {
            $channels = $report->channels;
            if (!is_array($channels)) {
                return;
            }

            $report->channels = ReportBlueprint::withCurrentMetricLabels($channels);
            $report->syncOriginalAttribute('channels');
        });
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

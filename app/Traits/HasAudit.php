<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasAudit
{
    public static function bootHasAudit()
    {
        static::created(function ($model) {
            if (!$model->is_audit && empty($model->source_uuid)) {
                DB::afterCommit(function () use ($model) {
                    try {
                        $freshModel = $model->fresh();
                        if ($freshModel && !$freshModel->is_audit && !$freshModel->auditVersion()->exists()) {
                            $freshModel->copyToAudit();
                        }
                    } catch (\Throwable $e) {
                        Log::error('Auto copy to audit failed for model ' . get_class($model) . ' UUID ' . $model->uuid . ': ' . $e->getMessage());
                    }
                });
            }
        });
    }

    protected function keepApprovalsOnAudit(): bool
    {
        return false;
    }

    public function auditVersion()
    {
        return $this->hasOne(static::class, 'source_uuid', 'uuid')->where('is_audit', true);
    }

    public function originalVersion()
    {
        return $this->belongsTo(static::class, 'source_uuid', 'uuid')->where('is_audit', false);
    }

    public function copyToAudit(): self
    {
        $clone = $this->replicate();
        $clone->uuid = (string) Str::uuid();
        $clone->is_audit = true;
        $clone->source_uuid = $this->uuid;

        $clone->save();

        return $clone;
    }

    public function scopeOperasional($query)
    {
        return $query->where('is_audit', false);
    }

    public function scopeAudit($query)
    {
        return $query->where('is_audit', true);
    }
}
<?php
namespace App\Models\Concerns;
use App\Models\Scopes\TenantScope;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Hotel;
trait BelongsToTenant {
    protected static function bootBelongsToTenant(): void {
        static::addGlobalScope(new TenantScope);
        static::creating(function ($model): void {
            $tenantId = app(TenantContext::class)->id();
            if ($tenantId !== null) {
                if ($model->hotel_id !== null && (int) $model->hotel_id !== $tenantId) abort(403, 'Cross-tenant write rejected.');
                $model->hotel_id = $tenantId;
            }
        });
    }
    public function hotel(): BelongsTo { return $this->belongsTo(Hotel::class); }
}

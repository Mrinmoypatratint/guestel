<?php
namespace App\Models\Scopes;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
final class TenantScope implements Scope {
    public function apply(Builder $builder, Model $model): void {
        $hotelId = app(TenantContext::class)->id();
        if ($hotelId !== null) $builder->where($model->qualifyColumn('hotel_id'), $hotelId);
    }
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_platform_admin',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_platform_admin' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'hotel_users')
            ->withPivot(['is_owner', 'status'])
            ->withTimestamps();
    }

    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_users')
            ->withPivot(['role', 'status'])
            ->withTimestamps();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('hotel_id')
            ->withTimestamps();
    }

    public function propertyAccesses(): HasMany
    {
        return $this->hasMany(PropertyAccess::class);
    }

    public function belongsToHotel(int $hotelId): bool
    {
        return $this->is_active && $this->hotels()->whereKey($hotelId)->wherePivot('status', 'active')->exists();
    }

    public function belongsToRestaurant(int $restaurantId): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->restaurants()->whereKey($restaurantId)->wherePivot('status', 'active')->exists()) {
            return true;
        }

        // If user belongs to hotel that owns this restaurant, they have access
        $restaurant = Restaurant::find($restaurantId);
        return $restaurant && $restaurant->hotel_id && $this->belongsToHotel($restaurant->hotel_id);
    }

    public function hasPermission(string $permission, ?int $hotelId = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->is_platform_admin && $hotelId === null) {
            return true;
        }

        return $this->roles()
            ->wherePivot('hotel_id', $hotelId)
            ->whereHas('permissions', fn($q) => $q->where('name', $permission))
            ->exists();
    }

    public function primaryRole(?int $hotelId = null): ?Role
    {
        if ($this->is_platform_admin) {
            return new Role(['name' => 'SUPER_ADMIN', 'label' => 'Company Super Admin']);
        }

        return $this->roles()->wherePivot('hotel_id', $hotelId)->first();
    }

    public function workspaceRoute(?int $hotelId = null): string
    {
        if ($this->is_platform_admin) {
            return route('platform.dashboard');
        }

        $role = $this->primaryRole($hotelId);
        $roleName = $role?->name;

        if ($roleName === 'HOUSEKEEPING_LEAD' || ($this->hasPermission('requests.view', $hotelId) && !$this->hasPermission('hotel.view', $hotelId))) {
            return route('admin.requests.index');
        }

        if ($roleName === 'EXECUTIVE_CHEF' || $roleName === 'RESTAURANT_MANAGER' || ($this->hasPermission('orders.view', $hotelId) && !$this->hasPermission('hotel.view', $hotelId))) {
            return route('admin.orders.index');
        }

        return route('admin.dashboard');
    }

    /**
     * Get all authorized properties (hotels or restaurants) according to role
     */
    public function accessibleProperties(): array
    {
        $list = [];

        $isChef = $this->roles()->whereHas('permissions', fn($q) => $q->where('name', 'orders.view'))->exists()
            && !$this->roles()->whereHas('permissions', fn($q) => $q->where('name', 'hotel.view'))->exists();

        $isHousekeeper = $this->roles()->whereHas('permissions', fn($q) => $q->where('name', 'requests.view'))->exists()
            && !$this->roles()->whereHas('permissions', fn($q) => $q->where('name', 'hotel.view'))->exists();

        // 1. Chef / F&B Lead -> Only restaurants
        if ($isChef) {
            $restaurants = $this->restaurants()->wherePivot('status', 'active')->where('is_active', true)->get();
            if ($restaurants->isEmpty()) {
                $hotelIds = $this->hotels()->wherePivot('status', 'active')->pluck('hotels.id');
                $restaurants = Restaurant::withoutGlobalScopes()->whereIn('hotel_id', $hotelIds)->where('is_active', true)->get();
            }
            foreach ($restaurants as $rest) {
                $list[] = [
                    'type' => 'restaurant',
                    'id' => $rest->id,
                    'hotel_id' => $rest->hotel_id,
                    'name' => $rest->name,
                    'city' => $rest->hotel?->city ?? 'India',
                    'role' => 'Executive Chef / F&B',
                    'active_orders' => Order::withoutGlobalScopes()->where('restaurant_id', $rest->id)->whereIn('status', ['PENDING', 'ACCEPTED', 'PREPARING'])->count(),
                    'image' => $rest->image_path ? asset('storage/' . $rest->image_path) : null,
                ];
            }
            return $list;
        }

        // 2. Housekeeping Lead -> Hotels with housekeeping access
        if ($isHousekeeper) {
            $hotels = $this->hotels()->wherePivot('status', 'active')->where('hotels.status', 'active')->get();
            foreach ($hotels as $hotel) {
                $list[] = [
                    'type' => 'hotel',
                    'id' => $hotel->id,
                    'name' => $hotel->name,
                    'city' => $hotel->city ?? 'India',
                    'role' => 'Housekeeping Lead',
                    'rooms_count' => Room::withoutGlobalScopes()->where('hotel_id', $hotel->id)->count(),
                    'active_requests' => ServiceRequest::withoutGlobalScopes()->where('hotel_id', $hotel->id)->whereIn('status', ['PENDING', 'IN_PROGRESS', 'ASSIGNED'])->count(),
                    'image' => $hotel->branding?->cover_image_path ? asset('storage/' . $hotel->branding->cover_image_path) : null,
                ];
            }
            return $list;
        }

        // 3. Hotel Admin / Multi-Property Executive -> Authorized Hotels
        $hotels = $this->hotels()->wherePivot('status', 'active')->where('hotels.status', 'active')->get();
        foreach ($hotels as $hotel) {
            $role = $this->primaryRole($hotel->id);
            $roleLabel = $role ? $role->label : ($hotel->pivot->is_owner ? 'Property Owner' : 'Hotel Staff');

            $list[] = [
                'type' => 'hotel',
                'id' => $hotel->id,
                'name' => $hotel->name,
                'city' => $hotel->city ?? 'India',
                'role' => $roleLabel,
                'rooms_count' => Room::withoutGlobalScopes()->where('hotel_id', $hotel->id)->count(),
                'active_requests' => ServiceRequest::withoutGlobalScopes()->where('hotel_id', $hotel->id)->whereIn('status', ['PENDING', 'IN_PROGRESS', 'ASSIGNED'])->count(),
                'image' => $hotel->branding?->cover_image_path ? asset('storage/' . $hotel->branding->cover_image_path) : null,
            ];
        }

        return $list;
    }
}

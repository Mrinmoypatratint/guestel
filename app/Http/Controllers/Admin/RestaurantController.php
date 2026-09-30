<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{MenuCategory, MenuItem, Restaurant};
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RestaurantController extends Controller
{
    public function index()
    {
        return view('admin.restaurant.index', [
            'restaurants' => Restaurant::with(['categories', 'menuItems.modifiers'])->get(),
        ]);
    }

    public function storeRestaurant(Request $r)
    {
        $d = $r->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:30'],
            'currency' => ['required', 'size:3'],
        ]);

        Restaurant::create([...$d, 'is_active' => true]);

        return back()->with('status', 'Restaurant created successfully.');
    }

    public function storeCategory(Request $r, TenantContext $t)
    {
        $d = $r->validate([
            'restaurant_id' => ['required', Rule::exists('restaurants', 'id')->where(fn ($q) => $q->where('hotel_id', $t->requireId()))],
            'name' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        MenuCategory::create([...$d, 'is_active' => true]);

        return back()->with('status', 'Menu category created.');
    }

    public function storeItem(Request $r, TenantContext $t)
    {
        $d = $r->validate([
            'restaurant_id' => ['required', Rule::exists('restaurants', 'id')->where(fn ($q) => $q->where('hotel_id', $t->requireId()))],
            'menu_category_id' => ['nullable', Rule::exists('menu_categories', 'id')->where(fn ($q) => $q->where('hotel_id', $t->requireId()))],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'preparation_minutes' => ['required', 'integer', 'min:1', 'max:240'],
        ]);

        MenuItem::create([...$d, 'is_available' => true]);

        return back()->with('status', 'Menu item created.');
    }

    public function toggleItem(MenuItem $item, TenantContext $t)
    {
        abort_unless((int) $item->hotel_id === $t->requireId(), 403);
        $item->update(['is_available' => !$item->is_available]);

        return back()->with('status', "{$item->name} availability toggled to " . ($item->is_available ? 'Available' : 'Unavailable') . '.');
    }
}

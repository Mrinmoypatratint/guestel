<?php
namespace App\Support;
use App\Models\Hotel;
use RuntimeException;
final class TenantContext {
    private ?Hotel $hotel = null;
    public function set(Hotel $hotel): void { $this->hotel = $hotel; }
    public function clear(): void { $this->hotel = null; }
    public function hotel(): ?Hotel { return $this->hotel; }
    public function id(): ?int { return $this->hotel?->getKey(); }
    public function requireId(): int { return $this->id() ?? throw new RuntimeException('Tenant context is required.'); }
}

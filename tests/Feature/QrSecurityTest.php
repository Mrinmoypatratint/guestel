<?php
namespace Tests\Feature;use App\Models\QrCode;use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class QrSecurityTest extends TestCase{use RefreshDatabase;public function test_unknown_public_qr_token_returns_404_without_internal_error():void{$this->get('/g/not-a-real-token')->assertNotFound();}}

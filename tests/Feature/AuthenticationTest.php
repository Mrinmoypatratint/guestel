<?php
namespace Tests\Feature;use App\Models\User;use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class AuthenticationTest extends TestCase{use RefreshDatabase;public function test_login_regenerates_authenticated_session():void{$u=User::factory()->create(['password'=>'VeryStrongPassword123!','is_active'=>true]);$this->post('/login',['email'=>$u->email,'password'=>'VeryStrongPassword123!'])->assertRedirect();$this->assertAuthenticatedAs($u);}}

<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\ApiTestCase;

class ShopTest extends ApiTestCase
{
    public function test_shop_home_renders(): void
    {
        $this->get('/shop')
            ->assertOk()
            ->assertSee('Catalog Shop')
            ->assertSee('Full-stack Laravel');
    }

    public function test_shop_item_list_empty(): void
    {
        $this->get('/shop/items')
            ->assertOk()
            ->assertSee('No items match');
    }

    public function test_shop_item_detail(): void
    {
        $token = $this->createAuthenticatedToken();

        $itemId = $this->withHeaders($this->bearerHeaders($token))
            ->postJson('/items', ['name' => 'Shop Widget', 'price' => 9.99])
            ->json('id');

        $this->get('/shop/items/'.$itemId)
            ->assertOk()
            ->assertSee('Shop Widget');
    }

    public function test_shop_create_requires_login(): void
    {
        $this->get('/shop/items/new')
            ->assertRedirect('/shop/login');
    }

    public function test_shop_create_item(): void
    {
        $user = $this->createShopUser();

        $this->actingAs($user)
            ->get('/shop/items/new')
            ->assertOk();

        $response = $this->actingAs($user)->post('/shop/items/new', [
            'name' => 'Browser Widget',
            'description' => 'Added via HTML form',
            'price' => '12.50',
        ]);

        $response->assertRedirect();
        $this->followRedirects($response)
            ->assertSee('Browser Widget');
    }

    public function test_shop_login_page(): void
    {
        $user = $this->createShopUserEntity();

        $this->get('/shop/login')
            ->assertOk()
            ->assertSee('Browser session auth');

        $this->post('/shop/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect('/shop');
    }

    public function test_shop_register_page(): void
    {
        $this->get('/shop/register')
            ->assertOk()
            ->assertSee('Create account');
    }

    public function test_shop_register_creates_user_and_logs_in(): void
    {
        $this->post('/shop/register', [
            'email' => 'newshopper@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/shop');

        $this->assertAuthenticatedAs(User::query()->where('email', 'newshopper@example.com')->first());
    }

    public function test_shop_register_duplicate_email(): void
    {
        $this->createShopUserEntity();

        $this->from('/shop/register')
            ->post('/shop/register', [
                'email' => 'shopper@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertOk()
            ->assertSee('already exists');
    }

    private function createShopUserEntity(): User
    {
        return User::query()->create([
            'email' => 'shopper@example.com',
            'password' => 'secret123',
        ]);
    }

    private function createShopUser(): User
    {
        $user = $this->createShopUserEntity();
        $this->post('/shop/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect('/shop');

        return $user;
    }
}

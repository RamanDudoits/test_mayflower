<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class VisitApiTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Redis::del('country:visits');
    }

    public function test_increment_valid_country()
    {
        $this->postJson('/api/v1/visit', ['country' => 'ru'])
            ->assertStatus(200)
            ->assertJson(['status' => 'ok']);

        $this->assertEquals(1, Redis::hget('country:visits', 'ru'));
    }

    public function test_increment_invalid_country()
    {
        $this->postJson('/api/v1/visit', ['country' => 'belarus'])
            ->assertStatus(422);
    }

    public function test_stats()
    {
        Redis::hincrby('country:visits', 'us', 2);
        Redis::hincrby('country:visits', 'it', 3);

        $this->getJson('/api/v1/stats')
            ->assertStatus(200)
            ->assertJson(['us' => 2, 'it' => 3]);
    }
}

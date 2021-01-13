<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;
use Mockery\MockInterface;
use App\Repositories\PlayerRepositoryInterface;

class BettingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        // TODO: create player using Factory
        $player = [
            'id' => 2,
            'balance' => '500',
            "created_at" => "2021-01-13T08:34:25.000000Z",
            "updated_at" => "2021-01-13T08:34:26.000000Z",
            "deleted_at" => null
        ];
        $this->instance(
            PlayerRepositoryInterface::class,
            Mockery::mock(PlayerRepositoryInterface::class, function (MockInterface $mock) use($player){
                $mock->shouldReceive('find')->with(2)->once()->andReturn($player);
                $mock->shouldReceive('find')->with(1)->once()->andReturn([]);
            })
        );
        $response = $this->get('api/player/1');
        $response
            ->assertStatus(201)
            ->assertExactJson($player);
    }
}

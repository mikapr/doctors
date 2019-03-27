<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{


    public function testSlotsBlockTest()
    {
        \Auth::loginUsingId(1);

        $response = $this->post('/block-slot', [
            'doctor' => 1, 'day' => '2015-01-01', 'slot' => 3
        ]);

        $response->assertStatus(200);

        $blocked = \Cache::has('blockedSlots') ? \Cache::get('blockedSlots') : [];

        $this->assertTrue(isset($blocked['1_2015-01-01_3']));

        $response = $this->post('/unblock-slot', [
            'doctor' => 1, 'day' => '2015-01-01', 'slot' => 3
        ]);

        $blocked = \Cache::has('blockedSlots') ? \Cache::get('blockedSlots') : [];

        $this->assertTrue(!isset($blocked['1_2015-01-01_3']));
    }
}

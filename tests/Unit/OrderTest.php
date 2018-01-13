<?php

namespace Tests\Feature;

use App\Event;
use App\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function test_that_entries_are_released_when_an_order_is_cancelled()
    {
        $event = factory(Event::class)->create()->addEntries(12);
        $order = $event->registerEntry('emma@example.com', 4);
        $this->assertEquals(8, $event->entriesRemaining);

        $order->cancel();

        $this->assertEquals(12, $event->entriesRemaining);
        $this->assertNull(Order::find($order->id));
    }
}

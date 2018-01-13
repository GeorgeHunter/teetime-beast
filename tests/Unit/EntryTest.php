<?php

namespace Tests\Unit;

use App\Event;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EntryTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_ticket_can_be_released()
    {
        $event = factory(Event::class)->create();
        $event->addEntries(1);
        $order = $event->registerEntry('john@example.com', 1);
        $entry = $event->entries()->first();
        $this->assertEquals($order->id, $entry->order_id);

        $entry->release();

        $this->assertNull($entry->fresh()->order_id);
    }
}

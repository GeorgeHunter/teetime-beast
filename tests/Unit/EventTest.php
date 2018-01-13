<?php

namespace Tests\Unit;

use App\Event;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Psy\Output\ShellOutput;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function can_get_formatted_date()
    {
        $event = factory(Event::class)->make([
            'date' => Carbon::parse('2018-05-01 8:00am'),
        ]);

        $this->assertEquals('May 1, 2018', $event->formatted_date);
    }
    
    /** @test */
    function can_get_formatted_start_time()
    {
        $event = factory(Event::class)->make([
            'date' => Carbon::parse('2018-05-01 09:00:00'),
        ]);

        $this->assertEquals('9:00am', $event->formatted_start_time);
    }

    /** @test */
    public function can_get_event_price_in_pounds()
    {
        $event = factory(Event::class)->make([
            'entry_fee' => 3500
        ]);

        $this->assertEquals('35.00', $event->entry_fee_in_pounds);
    }
    
    /** @test */
    function events_with_a_published_at_date_are_published()
    {
        $publishedEventA = factory(Event::class)->states('published')->create();
        $publishedEventB = factory(Event::class)->states('published')->create();
        $unpublishedEvent = factory(Event::class)->states('unpublished')->create();

        $publishedEvents = Event::published()->get();

        $this->assertTrue($publishedEvents->contains($publishedEventA));
        $this->assertTrue($publishedEvents->contains($publishedEventB));
        $this->assertFalse($publishedEvents->contains($unpublishedEvent));
    }

    /** @test */
    function can_register_entry_for_event()
    {
        $event = factory(Event::class)->create();

        $order = $event->registerEntry('emma@example.com', 3);

        $this->assertEquals('emma@example.com', $order->email);
        $this->assertEquals(3, $order->entries->count());
    }
    
    /** @test */
    function can_add_tickets()
    {
        $event = factory(Event::class)->create();

        $event->addEntries(40);


        $this->assertEquals(40, $event->entriesRemaining());

    }
}

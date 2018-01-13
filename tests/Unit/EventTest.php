<?php

namespace Tests\Unit;

use App\Event;
use App\Exceptions\InsufficientAvailableEntriesException;
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
        $event = factory(Event::class)->create()->addEntries(3);

        $order = $event->registerEntry('emma@example.com', 3);

        $this->assertEquals('emma@example.com', $order->email);
        $this->assertEquals(3, $order->entry_count);
    }
    
    /** @test */
    function can_add_entries()
    {
        $event = factory(Event::class)->create()->addEntries(50);
        
        $this->assertEquals(50, $event->entriesRemaining);
    }

    /** @test */
    function entries_remaining_does_not_include_entries_associated_with_an_order()
    {
        $event = factory(Event::class)->create()->addEntries(50);
        $event->registerEntry('emma@example.com', 3);

        $this->assertEquals(47, $event->entriesRemaining);

    }
    
    /** @test */
    function trying_to_purchase_more_tickets_than_remain_throws_an_exception()
    {
        $event = factory(Event::class)->create()->addEntries(10);

        try {
            $event->registerEntry('emma@example.com', 15);
        } catch(InsufficientAvailableEntriesException $e) {
            $this->assertFalse($event->hasOrderFor('emma@example.com'));
            $this->assertEquals(10, $event->entriesRemaining);
            return;
        }

        $this->fail("Order succeeded even though there were not enough entries available");
    }

    /** @test */
    function cannot_order_entry_which_has_already_been_purchased()
    {
        $event = factory(Event::class)->create()->addEntries(10);

        $event->registerEntry('emma@example.com', 8);

        try {
            $event->registerEntry('paul@example.com', 3);
        } catch(InsufficientAvailableEntriesException $e) {
            $this->assertFalse($event->hasOrderFor('paul@example.com'));
            $this->assertEquals(2, $event->entriesRemaining);
            return;
        }

        $this->fail("Order succeeded even though there were not enough entries available");
    }
}

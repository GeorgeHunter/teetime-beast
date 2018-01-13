<?php

namespace Tests\Feature;

use App\Event;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewEventListingsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function user_can_view_a_published_event_listing()
    {
        $event = factory(Event::class)->states('published')->create([
            'title' => "Long Sutton Mens Open",
            'subtitle' => 'Great prizes to be won',
            'date' => Carbon::parse('June 23, 2018 9:00am'),
            'entry_fee' => 2000,
            'course' => 'Long Sutton GC',
            'course_address' => 'Long Sutton Golf Club',
            'city' => 'Long Sutton',
            'county' => 'Somerset',
            'postcode' => 'LP12 4TT',
            'additional_information' => 'For more details contact John on 01963 321726'
        ]);

        $events = $this->get('/events/'.$event->id);

        $events->assertSee("Long Sutton Mens Open");
        $events->assertSee('Great prizes to be won');
        $events->assertSee('June 23, 2018');
        $events->assertSee('9:00am');
        $events->assertSee('20.00');
        $events->assertSee('Long Sutton GC');
        $events->assertSee('Long Sutton Golf Club');
        $events->assertSee('Long Sutton');
        $events->assertSee('Somerset');
        $events->assertSee('LP12 4TT');
        $events->assertSee('For more details contact John on 01963 321726');
    }
    
    /** @test */
    function user_cannot_view_unpublished_event_listings()
    {
        $event = factory(Event::class)->states('unpublished')->create();

        $events = $this->get('/events/'.$event->id);

        $events->assertStatus(404);
    }
}

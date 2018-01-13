<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Event;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PharIo\Version\ExactVersionConstraint;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseEntryTest extends TestCase
{
    use DatabaseMigrations;

    private $paymentGateway;

    protected function setUp()
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    private function enterEvent($event, $params) {
        return $this->json('POST', "/events/{$event->id}/orders", $params);
    }

    public function assertValidationError($request, $error)
    {
        $request->assertStatus(422);
        $this->assertArrayHasKey($error, $request->decodeResponseJson()['errors']);
    }

    /** @test */
    function customer_can_purchase_entry_to_a_published_event()
    {
        $this->withoutExceptionHandling();

        $event = factory(Event::class)->states('published')->create([
            'entry_fee' => 2500
        ]);

        $store = $this->enterEvent($event, [
            'email' => 'john@example.com',
            'entry_quantity' => 4,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $store->assertStatus(201);

        $this->assertEquals(10000, $this->paymentGateway->totalCharges());

        $order = $event->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(4, $order->entries()->count());
    }

    /** @test */
    function customer_cannot_purchase_entry_to_unpublished_events()
    {
        $event = factory(Event::class)->states(['unpublished'])->create();

        $store = $this->enterEvent($event, [
            'email' => 'john@example.com',
            'entry_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $store->assertStatus(404);
        $this->assertEquals(0, $event->orders->count());

        $this->assertEquals(0, $this->paymentGateway->totalCharges());

    }

    /** @test */
    function an_order_is_not_created_if_payment_fails()
    {
        $this->withoutExceptionHandling();
        $event = factory(Event::class)->states('published')->create();

        $store = $this->enterEvent($event, [
            'email' => 'john@example.com',
            'entry_quantity' => 3,
            'payment_token' => 'invalid-token',
        ]);

        $store->assertStatus(422);
        $order = $event->orders()->where('email', 'john@example.com')->first();
        $this->assertNull($order);
    }
    
    /** @test */
    function cannot_purchase_more_entries_than_remain()
    {
        $event = factory(Event::class)->states('published')->create();
        $event->addEntries(3);

        $store = $this->enterEvent($event, [
            'email' => 'john@example.com',
            'entry_quantity' => 4,
            'payment_token' => $this->paymementGateway->getValidPaymentToken(),
        ]);

        $store->assertStatus(422);
        $order = $event->entries()->where('email', 'john@example.com');
        $this->assertNull($order);
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(3, $event->entriesRemaining());
    }

    /** @test */
    function email_is_required_to_register_entry()
    {
        $event = factory(Event::class)->states('published')->create();

        $store = $this->enterEvent($event, [
            'email' => null,
            'entry_quantity' => 4,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($store, 'email');
    }

    /** @test */
    function entry_quantity_is_required_to_register_entry()
    {
        $event = factory(Event::class)->states('published')->create();

        $store = $this->enterEvent($event, [
            'email' => 'john@example.com',
            'entry_quantity' => null,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($store, 'entry_quantity');
    }

    /** @test */
    function entry_quantity_must_be_between_1_and_4()
    {
        $event = factory(Event::class)->states('published')->create();

        $store = $this->enterEvent($event, [
            'email' => 'john@example.com',
            'entry_quantity' => 5,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($store, 'entry_quantity');
    }

    /** @test */
    function payment_token_is_required()
    {
        $event = factory(Event::class)->states('published')->create();

        $store = $this->enterEvent($event, [
            'email' => 'john@example.com',
            'entry_quantity' => 3,
            'payment_token' => null,
        ]);

        $this->assertValidationError($store, 'payment_token');
    }

    
}

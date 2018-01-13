<?php

namespace App\Http\Controllers;

use App\Billing\FakePaymentGateway;
use app\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Event;
use App\Exceptions\InsufficientAvailableEntriesException;
use Illuminate\Http\Request;

class EventOrdersController extends Controller
{
    /**
     * @var FakePaymentGateway
     */
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($eventId)
    {
        $event = Event::published()->findOrFail($eventId);

        $this->validate(request(), [
            'email' => 'required',
            'entry_quantity' => 'required|numeric|min:1|max:4',
            'payment_token' => 'required'
        ]);

        try {
            $order = $event->registerEntry(request('email'), request('entry_quantity'));
            $this->paymentGateway->charge(request('entry_quantity') * $event->entry_fee, request('payment_token'));
            return response()->json([], 201);
        } catch (PaymentFailedException $e) {
            $order->cancel();
            return response()->json([], 422);
        } catch (InsufficientAvailableEntriesException $e) {
            return response()->json([], 422);
        }

    }
}

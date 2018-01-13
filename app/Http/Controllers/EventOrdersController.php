<?php

namespace App\Http\Controllers;

use App\Billing\FakePaymentGateway;
use app\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Event;
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
            $this->paymentGateway->charge(request('entry_quantity') * $event->entry_fee, request('payment_token'));
            $order = $event->registerEntry(request('email'), request('entry_quantity'));
        } catch (PaymentFailedException $e) {
            return response()->json([], 422);
        }

        return response()->json([], 201);
    }
}

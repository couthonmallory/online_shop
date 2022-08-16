<?php

namespace App\Http\Controllers;

use Error;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use App\Repositories\CartRepository;

class StripeCheckoutController extends Controller
{
    public function create()
    {
        return view('checkout.create');
    }

    public function paymentIntent(){
        
                // This is your test secret API key.
        \Stripe\Stripe::setApiKey(config('stripe.test_secret_key'));

        $cartTotal = (new CartRepository())->total();

        header('Content-Type: application/json');

        try {
            // retrieve JSON from POST body
            $jsonStr = file_get_contents('php://input');
            $jsonObj = json_decode($jsonStr);

            // Create a PaymentIntent with amount and currency
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $cartTotal,
                'currency' => 'eur',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'order_items' => (new CartRepository())->jsonOrderItems()
                ]
            ]);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];

            echo json_encode($output);
        } catch (Error $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

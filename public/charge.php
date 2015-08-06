<?php
require_once __DIR__ . '/../vendor/autoload.php';

error_log(json_encode($_POST));

if(!isset($_POST['token'])){
    return;
}

\Stripe\Stripe::setApiKey(getenv('STRIPE_KEY'));

$customer = \Stripe\Customer::create(array(
    "metadata" => ["venture" => true, "created" => 'venture'],
    "source" => $_POST['token'], // obtained with Stripe.js
    "email" => $_POST['email'],
));

error_log('created customer for: ' . $_POST['email']);
error_log('customer id: ' . $customer['id']);

$charge = \Stripe\Charge::create(array(
    "amount" => 3500,
    "currency" => "USD",
    "customer" => $customer['id'],
    "description" => "Venture Early Bird Ticket"
));

error_log('charge id: ' . $charge['id']);

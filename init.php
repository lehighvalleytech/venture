<?php
chdir(__DIR__);
require_once 'vendor/autoload.php';

if(file_exists('local.php')){
    include('local.php');
}

\Stripe\Stripe::setApiKey(getenv('STRIPE_KEY'));
$sendgrid = new SendGrid(getenv('SENDGRID_KEY'));
$orch = new \andrefelipe\Orchestrate\Client();
$lvtech  = new \andrefelipe\Orchestrate\Application();
<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// idempotent queue declaration
$channel->queue_declare('hello', false, false, false, false);
$msg = new AMQPMessage('Testing is done here!');
$channel->basic_publish($msg, '', 'hello');

echo "message is sent";

$channel->close();
try {
    $connection->close();
} catch (Exception $e) {
    echo $e->getTraceAsString();
}

<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// idempotent queue declaration
$channel->queue_declare('hello', false, false, false, false);
echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
};
$channel->basic_consume('hello', '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    try {
        $channel->wait();
    } catch (ErrorException $e) {
        echo $e->getTraceAsString();
    }
}

$channel->close();
try {
    $connection->close();
} catch (Exception $e) {
    echo $e->getTraceAsString();
}

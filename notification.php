<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/DotEnv.php';

(new DotEnv())->load();

use PhpAmqpLib\Connection\AMQPStreamConnection;

$host = getenv('RABBITMQ_HOST');
$port = getenv('RABBITMQ_PORT');
$vhost = getenv('RABBITMQ_VHOST');
$user = getenv('RABBITMQ_USERNAME');
$password = getenv('RABBITMQ_PASSWORD');

$queue = getenv('RABBITMQ_QUEUE');

try {
    $rabbitConnection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
} catch (Exception $e) {
    die("Could not connect to RabbitMQ: " . $e->getMessage());
}

$dbConnection = Database::getConnection();
$channel = $rabbitConnection->channel();

$callback = function ($msg) use ($dbConnection): void
{
    $data = json_decode($msg->body, true);

    $postId = $data['id'];
    $action = $data['action'];

    $statement = $dbConnection->prepare("INSERT INTO post_notifications (post_id, message) VALUES (:post_id, :message)");

    $message = "Post with ID {$postId} was {$action}";

    $statement->bindParam(':post_id', $postId);
    $statement->bindParam(':message', $message);
    $statement->execute();
};

$channel->queue_declare($queue, false, true, false, false);

$channel->basic_consume($queue, '', false, true, false, false, $callback);
echo 'Waiting for new message on queue', " \n";

while ($channel->is_consuming()) {
    $channel->wait();
}
$channel->close();
$rabbitConnection->close();

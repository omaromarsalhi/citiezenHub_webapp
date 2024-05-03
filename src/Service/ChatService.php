<?php

namespace App\Service;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class ChatService implements WampServerInterface
{
    protected $clients = []; // Array to store connected clients

    public function onSubscribe(ConnectionInterface $conn, $topic): void
    {
        // Add client to the list
        $this->clients[$conn->resourceId] = $conn;
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic): void
    {
        // Remove client from the list
        unset($this->clients[$conn->resourceId]);
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        $event = $params[0];
        $data = $params[1] ?? null;

        switch ($event) {
            case 'message':
                // Broadcast message to all connected clients
                foreach ($this->clients as $client) {
                    if ($client !== $conn) {
                        $client->call('onMessage', [$params]);
                    }
                }
                break;
            // Add more cases for other events if needed
        }
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        // Not used in this basic example
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        // Handle connection errors
        echo "An error occurred: {$e->getMessage()}\n";
    }

    public function onOpen(ConnectionInterface $conn, ?\Psr\Http\Message\RequestInterface $request = null)
    {
        // Handle connection opening and potentially the request
        // You can add custom logic here if needed
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Handle connection closing
        // You can add custom logic here if needed
    }
}

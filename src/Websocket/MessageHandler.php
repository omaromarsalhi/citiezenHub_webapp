<?php

// src/Websocket/MessageHandler.php
namespace App\Websocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MessageHandler implements MessageComponentInterface
{

    private $userConnections = []; // User ID to Connection mapping


    public function onOpen(ConnectionInterface $conn): void
    {
        // Extract user ID from query string (adjust as needed)
        $queryString = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryString, $queryParameters);
        $userId = $queryParameters['userId'] ?? null;

        if ($userId) {
            // Store the connection based on user ID
            $this->userConnections[$userId] = $conn;
            echo "New connection for user {$userId}! ({$conn->resourceId})\n";
            echo "size ".sizeof($this->userConnections)."\n";
        } else {
            // Handle cases where user ID is missing
            echo "User ID not provided. Connection rejected.\n";
            $conn->close();
        }
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $data = json_decode($msg, true);

        if (isset($data['senderId'], $data['recipientId'], $data['message'])) {
            $senderId = $data['senderId'];
            $recipientId = $data['recipientId'];

            // Look up recipient's connection based on recipient's ID
            $recipientConnection = $this->userConnections[$recipientId] ?? null;
            $senderConnection = $this->userConnections[$senderId] ?? null;

            if ($recipientConnection && $recipientConnection !== $from && $senderConnection===$from ) {
                $encoders = [new JsonEncoder()];
                $normalizers = [new ObjectNormalizer()];

                $serializer = new Serializer($normalizers, $encoders);
                $json = $serializer->serialize($data, 'json');
                // Send the message to the recipient
                echo "data sent from {$senderId}! to ({$recipientConnection->resourceId})\n";

                $recipientConnection->send($json);

            }
        }

    }

    public function onClose(ConnectionInterface $conn): void
    {
        // Remove the connection when it's closed
        foreach ($this->userConnections as $userId => $connection) {
            if ($connection === $conn) {
                unset($this->userConnections[$userId]);
                break;
            }
        }
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        // Handle errors (e.g., log or close the connection)
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

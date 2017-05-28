<?php

namespace Acme\Pay\Service;

use Acme\Pay\Client\DbMapper;
use Acme\Pay\Exception\ClientAlreadyExists;
use Doctrine\DBAL;

class Clients
{
    /**
     * @var DBAL\Connection
     */
    private $db;

    /**
     * @param DBAL\Connection $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @param \stdClass $client http://acmepay.local/schema/client.json
     * @return int new client ID
     * @throws ClientAlreadyExists
     */
    public function create($client)
    {
        try {
            $this->db->insert('client', (new DbMapper($client))->clientsTableRow());
        } catch (DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ClientAlreadyExists('Name ' . $client->name . ' is already taken', 0, $e);
        }
        return $this->db->lastInsertId('client_id_seq');
    }
}

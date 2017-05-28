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
     * @return \stdClass $client http://acmepay.local/schema/client.json
     * @throws ClientAlreadyExists
     */
    public function create($client)
    {
        try {
            $this->db->insert('client', (new DbMapper($client))->clientsTableRow());
        } catch (DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new ClientAlreadyExists('Name ' . $client->name . ' is already taken', 0, $e);
        }
        $client->id = $this->db->lastInsertId('client_id_seq');
        return $client;
    }

    public function getByName($name)
    {
        $row = $this->db->fetchAssoc('SELECT * FROM client WHERE name = ?', [$name]);

        $client = new \stdClass();
        $client->id = $row['id'];
        $client->name = $row['name'];
        ($row['city'] !== null) && $client->city = $row['city'];
        ($row['country'] !== null) && $client->country = $row['country'];

        return $client;
    }
}

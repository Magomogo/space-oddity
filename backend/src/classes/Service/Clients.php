<?php

namespace Acme\Pay\Service;

use Acme\Pay\Client\DbMapper;
use Acme\Pay\Exception;
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
     * @throws Exception\ClientAlreadyExists
     */
    public function create($client)
    {
        try {
            $this->db->insert('client', (new DbMapper($client))->clientsTableRow());
        } catch (DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new Exception\ClientAlreadyExists('Name ' . $client->name . ' is already taken', 0, $e);
        }
        $client->id = $this->db->lastInsertId('client_id_seq');
        return $client;
    }

    /**
     * @param string $name
     * @return \stdClass http://acmepay.local/schema/client.json
     * @throws Exception\ClientDoesNotExists
     */
    public function getByName($name)
    {
        $row = $this->db->fetchAssoc('SELECT * FROM client WHERE name = ?', [$name]);

        if ($row === false) {
            throw new Exception\ClientDoesNotExists('Client ' . $name . ' does not exists');
        }

        $client = new \stdClass();
        $client->id = $row['id'];
        $client->name = $row['name'];
        ($row['city'] !== null) && $client->city = $row['city'];
        ($row['country'] !== null) && $client->country = $row['country'];

        return $client;
    }
}

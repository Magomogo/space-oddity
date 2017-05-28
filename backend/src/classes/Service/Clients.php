<?php

namespace Acme\Pay\Service;

use Acme\Pay\Client\DbMapper;
use Doctrine\DBAL\Connection;

class Clients
{
    /**
     * @var Connection
     */
    private $db;

    /**
     * @param Connection $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @param \stdClass $client http://acmepay.local/schema/client.json
     * @return integer new client ID
     */
    public function create($client)
    {
        $this->db->insert('client', (new DbMapper($client))->clientsTableRow());
        return $this->db->lastInsertId('client_id_seq');
    }
}

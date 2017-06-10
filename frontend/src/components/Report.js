import React from 'react';
import { Table } from 'react-bootstrap';

import Transaction from './Transaction';

export default () => (
    <Table striped bordered condensed hover>
        <thead>
        <tr>
            <th>#</th>
            <th>Client</th>
            <th>Timestamp</th>
            <th>Amount</th>
            <th>Balance change</th>
        </tr>
        </thead>
        <tbody>
            <Transaction/>
            <Transaction/>
            <Transaction/>
        </tbody>
    </Table>
);

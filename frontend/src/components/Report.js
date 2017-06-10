import React from 'react';
import { Table } from 'react-bootstrap';

import Transaction from './Transaction';

export default (props) => {

    const {listOfTransactions} = props;

    return (
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
            {
                listOfTransactions.length ?
                    listOfTransactions.map(
                        (t) => <Transaction key={t.id} transaction={t}/>
                    )
                    :
                    <tr><td colSpan={5} className="text-center">nothing here</td></tr>
            }
            </tbody>
        </Table>
    );
}

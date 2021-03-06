import React from 'react';
import { Table } from 'react-bootstrap';

import Transaction from './Transaction';
import Money from './Money';

export default (props) => {

    const {listOfTransactions} = props,
        wallet = listOfTransactions[0] && listOfTransactions[0].wallet;

    return (
        <Table striped bordered condensed hover>
            {wallet &&
                <caption>
                    Transactions wallet #{wallet.id},
                    {wallet.client.name} from {wallet.client.city}, {wallet.client.country}.
                    Balance: <b><Money amount={wallet.balance} currency={wallet.currency}/></b>
                </caption>}
            <thead>
            <tr>
                <th>#</th>
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
            {listOfTransactions.length ? <tbody>
                <tr>
                    <td colSpan={2}/>
                    <td>Summary:</td>
                    <td>
                        <Money amount={props.summary.USD} currency="USD"/>
                        {props.summary.own && ', '}
                        {props.summary.own && <Money amount={props.summary.own.sum} currency={props.summary.own.currency}/>}
                    </td>
                </tr>
            </tbody> : null}
        </Table>
    );
}

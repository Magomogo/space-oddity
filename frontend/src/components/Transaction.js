import React from 'react';

import Money from './Money';

export default (props) => {
    const { transaction } = props;

    return (
        <tr>
            <td>{ transaction.id }</td>
            <td>{ transaction.timestamp }</td>
            <td><Money amount={transaction.amount} currency={transaction.currency}/></td>
            <td><Money amount={transaction.balance_change} currency={transaction.wallet.currency}/></td>
        </tr>
    );
}

import React from 'react';

export default (props) => {
    const { transaction } = props,
        printMoney = (cents) => cents / 100;

    return (
        <tr>
            <td>{ transaction.id }</td>
            <td>{ transaction.wallet.client.name }</td>
            <td>{ transaction.timestamp }</td>
            <td>{ printMoney(transaction.amount) } { transaction.currency }</td>
            <td>{ printMoney(transaction.balance_change)} { transaction.wallet.currency }</td>
        </tr>
    );
}

import React from 'react';

export default (props) => {
    const { transaction } = props;

    return (
        <tr>
            <td>{ transaction.id }</td>
            <td>John Doe</td>
            <td>2017-01-01T00:32:22</td>
            <td>100 USD</td>
            <td>-5790 RUB</td>
        </tr>
    );
}

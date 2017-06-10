import React from 'react';

import SearchForm from './SearchForm';
import Report from './Report';

export default class TheApplication extends React.Component {

    constructor(props) {
        super(props);
        this.state = {transactions: []};
    }

    componentDidMount() {
        this.props
            .fetch(
                'http://acmepay.local/client/John/wallet/transactions',
                {headers: {Accept: 'application/json'}}
            )
            .then(response => response.json())
            .then((transactions) => {
                this.setState(
                    {
                        transactions: transactions
                    }
                );
            });
    }

    render() {
        return (
            <div>
                <SearchForm/>
                <h3>List of transactions</h3>
                <Report listOfTransactions={this.state.transactions}/>
            </div>
        );
    };
}

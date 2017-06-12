import React from 'react';

import SearchForm from './SearchForm';
import Report from './Report';

export default class TheApplication extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            clients: [],
            transactions: []
        };
        this.handleSearch = this.handleSearch.bind(this);
    }

    handleSearch(parameters) {
        this.props
            .fetch(
                'http://acmepay.local/client/' + encodeURIComponent(parameters.client.name) + '/wallet/transactions',
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

    componentDidMount() {
        this.props
            .fetch('http://acmepay.local/client')
            .then(response => response.json())
            .then((clients) => {
                this.setState({clients});
            });
    }

    render() {
        return (
            <div>
                <SearchForm clients={this.state.clients} handleSearch={this.handleSearch}/>
                <h3>List of transactions</h3>
                <Report listOfTransactions={this.state.transactions}/>
            </div>
        );
    };
}

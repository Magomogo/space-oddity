import React from 'react';
import { Button } from 'react-bootstrap';

import SearchForm from './SearchForm';
import Report from './Report';

export default class TheApplication extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            clients: [],
            transactions: [],
            summary: {},
            reportDownloadLink: null
        };
        this.handleSearch = this.handleSearch.bind(this);
    }

    handleSearch(parameters) {
        if (parameters.client) {
            const
                baseClientUri = 'http://acmepay.local/client/' + encodeURIComponent(parameters.client.name),
                searchQuery =
                    (parameters.startDate ? 'startDate=' + parameters.startDate + '&' : '')
                    + (parameters.endDate ? 'endDate=' + parameters.endDate : '');

            Promise.all([
                this.props
                    .fetch(
                        baseClientUri + '/wallet/transactions' + (searchQuery.length ? '?' + searchQuery : ''),
                        {headers: {Accept: 'application/json'}}
                    )
                    .then(response => response.json()),

                this.props
                    .fetch(
                        baseClientUri + '/wallet/summary' + (searchQuery.length ? '?' + searchQuery : '')
                    )
                    .then(response => response.json())
            ])
            .then(([transactions, summary]) => {
                this.setState({
                    transactions,
                    summary,
                    reportDownloadLink: baseClientUri + '/wallet/transactions' + (searchQuery.length ? '?' + searchQuery : '')
                });
            });
        }
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
                <Report listOfTransactions={this.state.transactions} summary={this.state.summary}/>
                {this.state.reportDownloadLink &&
                    <Button bsStyle="info" href={this.state.reportDownloadLink}>Download report as CSV</Button>}
            </div>
        );
    };
}

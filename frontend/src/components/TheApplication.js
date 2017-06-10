import React from 'react';

import SearchForm from './SearchForm';
import Report from './Report';

export default class TheApplication extends React.Component {

    constructor(props) {
        super(props);
        this.state = {transactions: []};
    }

    componentDidMount() {
        this.setState(
            {
                transactions: [
                    {
                        id: 1,
                    },
                    {
                        id: 2,
                    }
                ]
            }
        );
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

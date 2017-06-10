import React from 'react';

import SearchForm from './SearchForm';
import Report from './Report';

const listOfTransactions = [

    {
        id: 1,
    },
    {
        id: 2,
    }
];


export default () => (
    <div>
        <SearchForm/>
        <h3>List of transactions</h3>
        <Report listOfTransactions={listOfTransactions}/>
    </div>
);

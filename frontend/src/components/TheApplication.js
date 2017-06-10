import React from 'react';

import SearchForm from './SearchForm';
import Report from './Report';

export default () => (
    <div>
        <SearchForm/>
        <h3>List of transactions</h3>
        <Report/>
    </div>
);

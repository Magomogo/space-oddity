import React from 'react';
import ReactDOM from 'react-dom';
import 'bootstrap/dist/css/bootstrap.css';
import { Grid, PageHeader } from 'react-bootstrap';

import TheApplication from './components/TheApplication';

ReactDOM.render(
    <Grid>
        <PageHeader>
            ACME Pay
            <small>...the ultimate payment system you ever need</small>
        </PageHeader>
        <TheApplication/>
    </Grid>,
    document.getElementById('root')
);

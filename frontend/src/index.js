import React from 'react';
import ReactDOM from 'react-dom';
import 'bootstrap/dist/css/bootstrap.css';
import { Grid, PageHeader } from 'react-bootstrap';
import 'es6-promise/auto';
import fetch from 'isomorphic-fetch';

import TheApplication from './components/TheApplication';
import './styles.css';

ReactDOM.render(
    <Grid>
        <PageHeader>
            ACME Pay
            <small>...the ultimate payment system you ever need</small>
        </PageHeader>
        <TheApplication fetch={fetch}/>
    </Grid>,
    document.getElementById('root')
);

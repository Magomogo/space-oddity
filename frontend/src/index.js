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
        <a href="https://github.com/Magomogo/space-oddity">
            <img
                style={{position: 'absolute', top: 0, right: 0, border: 0}}
                src="https://camo.githubusercontent.com/e7bbb0521b397edbd5fe43e7f760759336b5e05f/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f677265656e5f3030373230302e706e67"
                alt="Fork me on GitHub"
                data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_green_007200.png"
            />
        </a>
    </Grid>,
    document.getElementById('root')
);

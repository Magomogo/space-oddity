import React from 'react';
import { Form, FormGroup, ControlLabel, FormControl } from 'react-bootstrap';

import ClientSelector from './ClientSelector';

export default () => (
    <Form inline>
        <FormGroup controlId="clientName">
            <ControlLabel>Name</ControlLabel>
            {' '}
            <ClientSelector/>
        </FormGroup>
        {' '}
        <FormGroup controlId="startDate">
            <ControlLabel>Start date</ControlLabel>
            {' '}
            <FormControl type="date" />
        </FormGroup>
        {' '}
        <FormGroup controlId="endDate">
            <ControlLabel>End date</ControlLabel>
            {' '}
            <FormControl type="date" />
        </FormGroup>
    </Form>
);

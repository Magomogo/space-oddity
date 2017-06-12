import React from 'react';
import { Form, FormGroup, ControlLabel, FormControl } from 'react-bootstrap';

import ClientSelector from './ClientSelector';

export default class TheApplication extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            client: null
        };
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleClientSelect = this.handleClientSelect.bind(this);
    }

    handleSubmit()
    {
        this.props.handleSearch(this.state);
    }

    handleClientSelect(client)
    {
        this.setState({client});
        this.props.handleSearch(Object.assign({}, this.state, {client}));
    }

    render () {
        return (
            <Form inline>
                <FormGroup controlId="clientName">
                    <ControlLabel>Name</ControlLabel>
                    {' '}
                    <ClientSelector clients={this.props.clients} selected={this.state.client} onSelect={this.handleClientSelect}/>
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
    }
}

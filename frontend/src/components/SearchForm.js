import React from 'react';
import { Form, FormGroup, ControlLabel, FormControl } from 'react-bootstrap';
import PropTypes from 'prop-types';

import ClientSelector from './ClientSelector';

class SearchForm extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            client: null,
            startDate: '',
            endDate: '',
        };
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleClientSelect = this.handleClientSelect.bind(this);
    }

    handleSubmit() {
        this.props.handleSearch(this.state);
    }

    handleClientSelect(client) {
        this.setState({client});
        this.props.handleSearch(Object.assign({}, this.state, {client}));
    }

    handleUserInput(val, name) {
        this.setState({[name]: val});
        this.props.handleSearch(Object.assign({}, this.state, {[name]: val}));
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
                    <FormControl type="date" value={this.state.startDate} onChange={(e) => this.handleUserInput(e.target.value, 'startDate')} />
                </FormGroup>
                {' '}
                <FormGroup controlId="endDate">
                    <ControlLabel>End date</ControlLabel>
                    {' '}
                    <FormControl type="date" value={this.state.endDate} onChange={(e) => this.handleUserInput(e.target.value, 'endDate')} />
                </FormGroup>
            </Form>
        );
    }
}

SearchForm.propTypes = {
    clients: PropTypes.array.isRequired,
    handleSearch: PropTypes.func.isRequired
};

export default SearchForm;

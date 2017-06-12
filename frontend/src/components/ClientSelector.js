import React from 'react';
import Select from 'react-select';
import 'react-select/dist/react-select.css';
import PropTypes from 'prop-types';

const ClientSelector = (props) => (
    <Select
        name="form-field-name"
        value={props.selected}
        clearable={false}
        options={props.clients.map((c) => { return {value: c, label: c.name}})}
        onChange={(def) => props.onSelect(def.value)}
        valueRenderer={(c) => c.name}
    />
);

ClientSelector.propTypes = {
    clients: PropTypes.array.isRequired,
    onSelect: PropTypes.func.isRequired,
    selected: PropTypes.object
};

export default ClientSelector

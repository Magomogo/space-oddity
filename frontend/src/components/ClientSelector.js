import React from 'react';
import Select from 'react-select';
import 'react-select/dist/react-select.css';

export default (props) => (
    <Select
        name="form-field-name"
        value={props.selected}
        options={props.clients.map((c) => { return {value: c, label: c.name}})}
        onChange={(def) => props.onSelect(def.value)}
        valueRenderer={(c) => c.name}
    />
);

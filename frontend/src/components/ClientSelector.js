import React from 'react';
import { FormControl } from 'react-bootstrap';

export default () => (
    <FormControl componentClass="select" placeholder="select">
        <option value="select">select</option>
        <option value="other">...</option>
    </FormControl>
);

import React from 'react';
import PropTypes from 'prop-types';

function Money (props) {
    const { amount, currency } = props,
        printMoney = (cents) => (cents / 100).toFixed(2);

    return (<span>{currency} {printMoney(amount)}</span>);
}

Money.propTypes = {
    amount: PropTypes.number.isRequired,
    currency: PropTypes.string.isRequired
};

export default Money;

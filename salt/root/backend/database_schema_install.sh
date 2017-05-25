#!/usr/bin/env bash
echo "

CREATE TABLE client (
    id SERIAL PRIMARY KEY,
    name TEXT,
    country TEXT,
    city TEXT
);

CREATE TABLE currency (
    code CHAR(3) PRIMARY KEY,
    rate FLOAT NOT NULL
);

CREATE TABLE currency_rates (
    \"date\" DATE,
    code CHAR(3),
    rate FLOAT NOT NULL,

    CONSTRAINT currency_rates_archive_pkey PRIMARY KEY (date, code)
);

CREATE TABLE wallet (
    id SERIAL PRIMARY KEY,
    client_id INT NOT NULL,
    currency CHAR(3) NOT NULL,
    balance INT DEFAULT 0,

    CONSTRAINT wallet_client_id_fk FOREIGN KEY (client_id) REFERENCES client (id),
    CONSTRAINT wallet_currency_fk FOREIGN KEY (currency) REFERENCES currency (code)
);

CREATE TABLE transaction (
    id SERIAL PRIMARY KEY,
    \"timestamp\" TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    currency CHAR(3) NOT NULL,
    amount INT NOT NULL,

    CONSTRAINT transaction_currency_fk FOREIGN KEY (currency) REFERENCES currency (code)
);

CREATE TABLE transfer (
    transaction_id INT NOT NULL,
    wallet_id INT NOT NULL,
    amount INT NOT NULL,

    CONSTRAINT transfer_transaction_id_fk FOREIGN KEY (transaction_id) REFERENCES transaction (id),
    CONSTRAINT transfer_wallet_id_fk FOREIGN KEY (wallet_id) REFERENCES wallet (id),
    CONSTRAINT transfer_transaction_id_wallet_id_pk PRIMARY KEY (transaction_id, wallet_id)
);

" | psql -1 -U postgres -d acmepay

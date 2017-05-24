#!/usr/bin/env bash
echo "

CREATE TABLE client (
    id SERIAL,
    name TEXT,
    country TEXT,
    city TEXT
)

" | psql -U postgres -d acmepay

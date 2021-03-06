[![Build Status](https://travis-ci.org/Magomogo/space-oddity.svg?branch=master)](https://travis-ci.org/Magomogo/space-oddity)

# Space oddity

Take your protein pills and put your helmet on

# Installation

1. Install virtual box and vagrant.

    * [virtualbox.org](http://www.virtualbox.org/)
    * [vagrantup.com](http://www.vagrantup.com/)

1. Install vagrant host updater plugin:

        vagrant plugin install vagrant-hostsupdater

1. Up vagrant.

        vagrant up

1. Navigate to http://acmepay.local

# Control

## Create a new client

    curl -X POST -d '{"name": "John Doe"}' http://acmepay.local/client

## Create a wallet

    curl -X POST http://acmepay.local/client/John%20Doe/wallet/USD?balance=10000

## Define a currency rate

    curl -X POST http://acmepay.local/currency/USD/rate/1?date=2017-06-04

## Transfer some money

    curl -X PUT http://acmepay.local/wallet/2/transfer-to/1/amount/100

## Get list of transactions

    curl http://acmepay.local/client/John%20Doe/wallet/transactions
    curl -H 'accept: application/json'  acmepay.local/client/John/wallet/transactions

## Get a wallet summary

    curl http://acmepay.local/client/John%20Doe/wallet/summary

# Development

Start the VM:

vagrant up

Sync host machine file changes:

vagrant rsync-auto

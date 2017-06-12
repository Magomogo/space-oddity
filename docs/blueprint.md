FORMAT: 1A

# GET /client

Get list of clients

+ Response 200 (application/json)

# POST /client

Client registration. Name should be unique since it is in use to identify a client.

+ Request
    + Schema

        :[Client](http://acmepay.local/schema/client.json)

+ Response 201 (application/json)

    + Schema

        :[Client](http://acmepay.local/schema/client.json)

    + Headers

        Location: /client/{$clientName}

# POST /client/{$clientName}/wallet/{$currencyCode}?balance={$balance}

Create a wallet for a client

+ Parameters
    + clientName: John Doe (string, mandatory) - Id of a client
    + currencyCode: USD (string, mandatory) - ISO currency code
    + balance: 100000 (number, optional) - Start balance, cents, default 0

+ Response 201 (application/json)

    + Schema

        :[Wallet](http://acmepay.local/schema/wallet.json)

    + Headers

        Location: /wallet/{$id}

# PUT /wallet/{$fromWalletId}/transfer-to/{$toWalletId}/amount/{$amount}?currency={$which}

Transfer from one wallet to other.

+ Parameters
    + fromWalletId: 1 (number, mandatory) - Id of source wallet
    + toWalletId: 2 (number, mandatory) - Id of destination wallet
    + amount: 1 (number, mandatory) - Amount to transfer, cents
    + which: own (string, optional) - Which currency to use: 'own', 'their', default 'own'

+ Response 200 (application/json)
+ Response 400 (application/json)

# POST /currency/{$code}/rate/{$rate}?date={$date}

Define a currency rate.

+ Parameters
    + code: USD (string, mandatory) - ISO currency code
    + rate: 1.2345 (number, mandatory) - Currency rate
    + date: 2017-05-26 (string, optional) - Date for which rate is defined, default: tomorrow

+ Response 201 (application/json)
+ Response 409 (application/json)

# GET /client/{$clientName}/wallet/transactions

Get list of transactions for a client's wallet, sorted by time. Can be provided in JSON or CSV, depends of `Accept` header,
text/csv is by default.

+ Parameters
    + clientName: John (string, mandatory) - Name of a client
    + limit: 10 (number, optional) - Amount of transactions to provide 
    + offset: 0 (number, optional) - Offset from the beginning of the list
    + startDay: 2017-05-01 (string, optional) - Beginning of a period, date in YYYY-MM-DD format
    + endDay: 2017-05-26 (string, optional) - End of a period, date in YYYY-MM-DD format

+ Headers
    Accept: text/csv, application/json - what content type the client is able to understand.

+ Response 200 (application/json)
    + Schema

    :[Schema](http://acmepay.local/schema/transaction.json#/definitions/list)

+ Response 200 (text/csv)

# GET /client/{$clientName}/wallet/summary

Sum of operations for a period.

+ Parameters
    + clientName: John (string, mandatory) - Name of a client
    + startDay: 2017-05-01 (string, optional) - Beginning of a period, date in YYYY-MM-DD format
    + endDay: 2017-05-26 (string, optional) - End of a period, date in YYYY-MM-DD format

+ Response 200 (application/json)
    + Schema

    :[Schema](http://acmepay.local/schema/wallet-summary.json)

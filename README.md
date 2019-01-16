# Bitrix rest api sdk

A small tool to simplify communication with Bitrix REST API instances

## Installation
 ```
composer require akamap/bitrix-rest-api-sdk
 ```

## Usage examples
 ```
$b24Connection = new Akamap\BitrixRestApi\Connection(
    'some-bitrix-site.com',
    'webhook',
    'key_here',
    1        
);

print_r($b24Connection->profile());
 ```
or
 ```
$b24Connection->call('profile');
 ```
or
```
    $leadFields = [
        'CREATED_BY_ID' => $b24Connection->getApiUserId(),
        'SOURCE_ID' => 'SELF',
        'STATUS_ID' => 'NEW',
        'EMAIL' => 'mail@example.com',
        'COMPANY_ID' => null,
        'OPPORTUNITY' => 1000,
        'CURRENCY_ID' => 'USD',
        'TITLE' => 'New lead',
        'IS_RETURN_CUSTOMER' => 'Y',
        'ASSIGNED_BY_ID' => $b24Connection->getApiUserId()
    ];
    
    $newLeadId = $b24Connection->crmLeadAdd($leadFields);
``` 
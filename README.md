# Challenge

## Routes
```
route: [GET] api/user/balance
parameters: user_id
```
```
route: [POST] api/user/add-money
parameters: user_id, amount
```

## Job and Console Command
A job created for print total transaction amount for a given date. date param is optional and if not pass, today will be calculated.
```
php artisan day-total-transaction
php artisan day-total-transaction 2020-07-03
```

## Tests
I use sqlite for database when testings is run, make sure you have php7.4-sqlite installed.
```
php artisan test
```
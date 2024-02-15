## Simple Contact API

I have tried to create contact REST API using Laravel, for example:

- User API
- Contact API
- Address API

You can setup the apps using command:
 ```
 php artisan migrate:fresh --seed
 php artisan serve
 ```

 Or you want check our unit test by using command:
`php artisan test`

### User API
|URL |Method |Request Header|Request Body|
--- | --- | ---| ---|
|Register `/api/users`|POST|-| email, username, password|
|Login `/api/users/login`|POST|-|username, password|
|Get Current User `/api/users/current`|GET|Authorization:test-token|-|
|Update `/api/users/current`|PATCH|Authorization:test-token|username, password|
|Logout `/api/users/logout`|DELETE|Authorization:test-token|-|

### Contact API
|URL |Method |Request Header|Request Body|
--- | --- | ---| ---|
|Create `/api/contacts`|POST|Authorization:test-token| firstname, lastname, phone|
|Search `/api/contacts?name=first_name`|GET|Authorization:test-token|-|
|Detail `/api/contacts/{id}`|GET|Authorization:test-token|-|
|Update `/api/contacts/{id}`|PUT|Authorization:test-token|firstname, lastname, phone|
|Delete `/api/contacts/{id}`|DELETE|Authorization:test-token|-|

### Address API
|URL |Method |Request Header|Request Body|
--- | --- | ---| ---|
|Create `/api/contacts/{contact_id}/addresses`|POST|Authorization:test-token| street, city, province, country, postal_code|
|List `/api/contacts/{contact_id}/addresses`|GET|Authorization:test-token|-|
|Detail `/api/contacts/{contact_id}/addresses/{address_id}`|GET|Authorization:test-token|-|
|Update `/api/contacts/{contact_id}/addresses/{address_id}`|PUT|Authorization:test-token|firstname, lastname, phone|
|Delete `/api/contacts/{contact_id}/addresses/{address_id}`|DELETE|Authorization:test-token|-|

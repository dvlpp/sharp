#Authentication and rights management

Sharp shall not be responsible of the auth or access rights management of a particular application: implementation is your choice.

## 1. Config

To enable authentication, we simply indicate a Auth service class in the application config (`site.php`):

```
<?php

return [
	"name" => "Quincy",
	"auth_service" => '\Quincy\Sharp\SharpAuthentication'
];
```

## 2. Auth service

Then, we have to write this `Quincy\Sharp\SharpAuthentication` class, making it implements  `Dvlpp\Sharp\Auth\SharpAuth`:

```
<?php namespace Quincy\Sharp;

use Dvlpp\Sharp\Auth\SharpAuth;
use Auth;

class SharpAuthentication implements SharpAuth {

	 function checkAdmin()
	 {
		return Auth::user();
	 }

	 function login($login, $password)
	 {
		if (Auth::attempt(array('login' => $login, 'password' => $password)))
		{
			return Auth::user()->login;
		}
		return false;
	 }

	 function logout()
	 {
		Auth::logout();
	 }

	 function checkAccess($login, $type, $action, $key)
	 {
		return true;
	 }
}
```

We have 4 methods to implement:

- `checkAdmin()` must return a boolean is the current authenticated user has Sharp rights, meaning that he can login to Sharp.
- `login()` is responsible of check credentials and return either the logged user name, or false in case of failure
- `logout()` is obvious
- and finally, `checkAccess()` if specific to rights management, detailed below.

In this example, I wrote the class in maybe the simplest way. Of course, you could have a much more complex implementation, and it doesn't really matters: Sharp only need to know how to login a user, and if a user is logged in.

Once the class is written, we can test it by hitting any Sharp page, and we get a login form:

TODO LOGIN FORM




# Plankton: a RESTful API microframework

## Requirements

 - PHP 7.2
 - PHP cURL extension

## Installation

composer require foxdie/rest

## Table of content
- [Client](#client)
  * [Create a client](#create-a-client)
  * [GET example](#get-example)
    + [using callback](#using-callback)
    + [using magic](#using-magic)
  * [POST example](#post-example)
    + [using callback](#using-callback-1)
    + [using magic](#using-magic-1)
  * [PUT, PATCH and DELETE examples](#put--patch-and-delete-examples)
  * [Magic calls](#magic-calls)
    + [Spinal case](#spinal-case)
    + [Examples](#examples)
  * [Authentication strategy](#authentication-strategy)
    + [anonymous auth](#anonymous-auth)
    + [basic auth](#basic-auth)
    + [client credentials](#client-credentials)
- [Server](#server)
  * [Creating a server](#creating-a-server)
  * [Handling requests](#handling-requests)
    + [Using a config file](#using-a-config-file)
      - [Example of config file](#example-of-config-file)
      - [Configure the server](#configure-the-server)
    + [Using annotations](#using-annotations)
      - [@Route annotation](#-route-annotation)
      - [@Method annotation](#-method-annotation)
      - [@Exception annotation](#-exception-annotation)
  * [Registering controllers](#registering-controllers)
  * [Creating middlewares](#creating-middlewares)
  * [Registering the middlewares](#registering-the-middlewares)
- [OAuth2](#oauth2)
  * [Client Credentials Grant](#client-credentials-grant)
    + [Client](#client-1)
    + [Server](#server-1)
      - [Creating your own Access Token Provider](#creating-your-own-access-token-provider)
- [Logging](#logging)
  * [Client side](#client-side)
    + [Simple logger](#simple-logger)
    + [XML logger](#xml-logger)
    + [Custom logger](#custom-logger)
  * [Server side](#server-side)

## Client
### Create a client
```php
use Plankton\Client\Client;
	
$client = new Client(API_ENDPOINT);
```
Full example here: https://github.com/foxdie/rest/blob/master/Test/public/simple-client.php
### GET example
```php
$response = $client->get("/user");
```
#### using callback
```php
$client->get("/user", function(Response $response){
	echo $response;
});
```
#### using magic
```php
$response = $client->getUser();
```
### POST example
```php
$response = $client->post("/user", ["email" => "foo@bar.com"]);
```
#### using callback
```php
$client->post("/user", ["email" => "foo@bar.com"], function(Response $response){
	echo $response->getLocation();
});
```
#### using magic
```php
$response = $client->postUser(["email" => "foo@bar.com"]);
```
### PUT, PATCH and DELETE examples
Full example here: https://github.com/foxdie/rest/blob/master/Test/public/simple-client.php

### Magic calls
#### Spinal case
If you want to use magic calls, your routes must use the spinal case
Example:

	$client->getUserAccount()
will match the following route:

	GET /user-account
camel case and snake case are not supported
#### Examples
| call | route |
| --- | --- |
| $client->getUser(); | GET /user |
| $client->group(1)->getUser(); | GET /group/1/user |
| $client->group(1)->getUser(2); | GET /group/1/user/2 |
| $client->postUser([]); | POST /user |
| $client->group(1)->postUser([]); | POST /group/1/user |
| $client->deleteUser(1); | DELETE /user/1 |
| $client->user(1)->delete(); | DELETE /user/1 |
| $client->group(1)->deleteUser(2); | DELETE /group/1/user/2 |
| $client->group(1)->user(2)->delete(); | DELETE /group/1/user/2 |
| $client->group(1)->user()->delete(2); | DELETE /group/1/user/2 |
### Authentication strategy	
#### anonymous auth
```php
$client = new Client(API_ENDPOINT);
```
#### basic auth
```php
use Plankton\Client\Strategy\BasicAuthentication;

$client = new Client(API_ENDPOINT, new BasicAuthentication(USER, PASSWORD));
```
#### client credentials
```php
use Plankton\Client\Strategy\ClientCredentialsAuthentication;

$client = new Client(API_ENDPOINT, new ClientCredentialsAuthentication(
	CLIENT_ID, 
	CLIENT_SECRET,
	AUTHENTICATION_URL
));
```
The authorize and access/refresh token requests will be performed automatically.
The 3rd parameter is optionnal, the default value is "/token"
## Server
### Creating a server
```php
use Plankton\Server\Server;

$server = new Server();
$server->run();
```
Full example here: https://github.com/foxdie/rest/blob/master/Test/public/simple-server.php
### Creating controllers
You must create at least one controller which extends the abstract class Plankton\Server\Controller
```php	
use Plankton\Server\Controller;

class APIController extends Controller{
	public function getUser(int $id, Request $request): Response{
	}
	
	public function postUser(Request $request): Response{
	}
}
```
Your controller will contain one public method for each action of your API.

You can create routes in 2 different ways:
- using a config file
- using annotations

#### Using a config file
This will automatically disable the annotation parser. The routes are described in a YAML file
##### Example of config file
```yml
routes:
    get-user:
        path: /user/{id}
        method: GET
        controller: Test\Controller\APIController::getUser
    create-user:
        path: /user
        method: POST
        controller: Test\Controller\APIController::createUser
```	        
Full example here: https://github.com/foxdie/plankton/blob/master/Test/config/server.yml

##### Configuring the server
```php
use Plankton\Server\{Server, Config};

$server = new Server(new Config(CONFIG_PATH));
```
Full example here: https://github.com/foxdie/plankton/blob/master/Test/public/config-server.php       
#### Using annotations
```php
use Plankton\Server\Controller;

class APIController extends Controller{
	/**
	 * @Route(/user/{id})
	 * @Method(GET)
	 */
	public function getUser(int $id, Request $request): Response{
	}
	
	/**
	 * @Route(/user)
	 * @Method(POST)
	 */
	public function createUser(Request $request): Response{
	}
}
```
The routes will be created automatically according to the annotations @Route and @Method.

Full example here : https://github.com/foxdie/rest/blob/master/Test/Controller/APIController.php
##### @Route annotation
- accepts regular expresssions
- accepts placeholders: they will be passed as argument in the same order as they appear
- the spinal case is strongly recommended

You can add a route prefix to your controller:
```php	
/**
 * @Route(/user)
 */
class APIController extends Controller{
	/**
	 * @Route(/{id})
	 * @Method(GET)
	 */
	public function getUser(int $id, Request $request): Response{
	}
	
	/**
	 * @Route(/)
	 * @Method(POST)
	 */
	public function createUser(Request $request): Response{
	}
}
```
##### @Method annotation
Possible values are:
- GET
- POST
- PUT
- PATCH
- DELETE

##### @Exception annotation
```php
class APIController extends Controller{
	/**
	 * This will catch any \CustomNameSpace\CustomException
	 * @Exception(CustomNameSpace\CustomException)
	 */
	public function catchCustomException(Exception $e, Request $request): Response{
	}
	
	/**
	 * This will catch all other exceptions
	 * @Exception(*)
	 */
	public function catchException(Exception $e, Request $request): Response{
	}
}
```
### Registering controllers
```php
use Plankton\Server\Server;

$server = new Server();
$server
	->registerController(new APIController());
	->registerController(...);
	->run();
```
Full example here: https://github.com/foxdie/rest/blob/master/Test/public/simple-server.php
### Creating middlewares
(this is optionnal)
You must implement the Plankton\Server\Middleware interface.
The middlewares can handle both incoming requests and outgoing responses.
```php
use Plankton\Server\{Request, Response};
use Plankton\Server\{Middleware, RequestDispatcher};

class BasicAuthenticationMiddleware implements Middleware{
	public function process(Request $request, RequestDispatcher $dispatcher): Response{
		// ...
		return $dispatcher->process($request);
	}
}
```
Full example here: https://github.com/foxdie/rest/blob/master/Test/Middleware/BasicAuthenticationMiddleware.php
### Registering the middlewares
```php
use Plankton\Server\Server;

$server = new Server();
$server
	->addMiddleware(new BasicAuthenticationMiddleware())
	->addMiddleware(...)
	->registerController(new APIController())
	->run();
```
## OAuth2
### Client Credentials Grant
#### Client
```php
use Plankton\Client\Client;
use Plankton\Client\Strategy\ClientCredentialsAuthentication;
use Plankton\Response;

$client = new Client(API_ENDPOINT, new ClientCredentialsAuthentication(
	CLIENT_ID, 
	CLIENT_SECRET,
	AUTHENTICATION_URL
));
```
Full example here: 	
https://github.com/foxdie/rest/blob/master/Test/public/oauth2-client.php
#### Server
```php
use Plankton\Server\Server;
use OAuth2\Middleware\ClientCredentialsMiddleware;
use OAuth2\Provider\MemoryProvider;
use Test\Controller\APIController;

// Access Token provider
$provider = new MemoryProvider();
$provider->addClient(CLIENT_ID, CLIENT_SECRET);

$server = new Server();
$server
	->addMiddleware(new ClientCredentialsMiddleware($provider))
	->registerController(new APIController())
	->run();
```
Full example here:
https://github.com/foxdie/rest/blob/master/Test/public/oauth2-server.php
##### Creating your own Access Token Provider
All you have to do is to implement the AccessTokenProvider interface:
```php
use Plankton\OAuth2\Provider\AccessTokenProvider;
use Plankton\OAuth2\Token\{AccessToken, BearerToken};

class PDOProvider implements AccessTokenProvider{
	/**
	 * return a new/issued Access Token if you find a client matching the authentication parameters (id + secret)
	 */
	public function getAccessToken(string $client_id, string $client_secret): ?AccessToken{
	}

	/**
	 * return a new Access Token if the Refresh Token is valid
	 */
	public function refreshToken(string $refreshToken): ?AccessToken{
	}

	/**
	 * authorize or not the given Access Token
	 */
	public function isValidAccessToken(string $token): bool{
	}
}
```
## Logging
### Client side
#### Simple logger
```php
use Plankton\Logging\SimpleLogger;

$client->setLogger(new SimpleLogger());

// ... do some requests

foreach ($client->getLogger()->getLogs() as $request) {
	$response = $client->getLogger()->getLogs()[$request];
}
```
Full example here: https://github.com/foxdie/rest/blob/master/Test/public/simple-client.php
#### XML logger
```php
use Plankton\Logging\XMLLogger;

$client->setLogger(new XMLLogger());

// ... do some requests

header("Content-type: text/xml");
echo $client->getLogger()->getLogs()->asXML();
```
Full example here: https://github.com/foxdie/rest/blob/master/Test/public/oauth2-client.php
#### Custom logger
You have to implement the Plankton\Request\Logger interface:
```php
use Plankton\{Request,Response};
use Plankton\Request\Logger

class CustomLogger implements Logger{
	public function log(Request $request, Response $response = NULL): void{
	}
}
```
### Server side
You can easily log requests and responses by [adding a middleware](#creating-middlewares):
```php
use Plankton\{Request,Response};
use Plankton\Server\{Middleware, RequestDispatcher};

class LogMiddleware implements Middleware{
	public function process(Request $request, RequestDispatcher $dispatcher): Response{		
		$response = $dispatcher->process($request);
		
		// log $request and $response here
		
		return $response
	}
}
```

and then register the middleware(#registering-the-middlewares)
```php
use Plankton\Server\Server;
use Test\Controller\APIController;
use Test\Middleware\LogMiddleware;

$server = new Server();
$server
	->addMiddleware(new LogMiddleware())
	->registerController(new APIController())
	->run();
```
# Symfony 5 HTTP Cached Rest Api

This app implements an HTTP cached REST api with api key based security in Symfony. Be sure to checkout the published heroku app [https://symfony-5-http-cached-rest-api.herokuapp.com/api/project-category](https://symfony-5-http-cached-rest-api.herokuapp.com/api/project-category) to see it working in action! 

The app utilizes and properly configures, the following bundles...

- FOSHttpCacheBundle
- FOSRestBundle
- Symfony SecurityBundle
- DoctrineBundle
- DoctrineMigrationsBundle
- JMSSerializerBundle
- SensioFrameworkExtraBundle
- HautelookAliceBundle

The provided Docker Compose configuration simplifies the implemention of the development environment, and will handle starting up the app. The docker environment consists of the following..

- PHP 8.0 FPM
- PostgreSQL 13.1
- Caddy 2.3.0

Caddy provides automatic HTTPS, and will expose the app at port 443 via the domain specified in an `APP_URL` environment variable. Be sure to add this domain to your `etc/hosts` file.

## Features
- Docker implementation includes PHP FPM, PostgreSQL, and Caddy server.
- Fully function REST API with `GET`, `GET all`, `POST`, `PUT`, and `DELETE` functionality.
- API key based authentication for `POST`, `PUT`, and `DELETE` requests.
- Automatic invalidation of url's upon `POST`, `PUT` and `DELETE` requests.
- Sensitive information scrubbing at serialization (such as User passwords, and api keys).
- Automatic association resolving (Associated entities will be included in json). 
- Sort, limit, and offset results of `GET all` requests.
- Automatic HTTPS via Caddy Server.
- Enable and Disable HTTP Cache via `APP_CACHE` environment variable.
- Automatic encoding of User passwords via Doctrine Listener, upon Persist, and Modification.
- Automatic conversion of Markdown to HTML via Doctrine Listener, upon Persist, and Modification.
- Fixtures created via `hautelook/alice-bundle`.
- User, Category, Tag, Article, ProjectCategory, Project entities provided.

## Run the App

1. Rename `.env.dist` to `.env`. 
2. Run `docker compose up`.
3. Optionally install fixtures by running `docker exec -t app_php_1 /bin/sh -c "php bin/console hautelook:fixtures:load"`

**A note on POST, PUT, and DELETE Requests**:  
In order to run any `POST`, `PUT`, or `DELETE` requests, you'll need to send a valid API key in the `X_AUTH_TOKEN` header. To aquire an API key, you'll need to create an inital user by installing the fixtures. Passwords, and API keys, are scrubbed from the JSON during serialization, so in order to actually retrieve the api key, you'll need to load up the database in an app like TablePlus.

## FOSRestBundle

The app includes a couple of custom event listeners that enable automatically resolving entity associations, and resource names during the request. Take a look at `src/EventListener/AssociationNormalizingListener.php` and `/src/EventListener/ResourceResolvingListener.php` to see how they work.

The entire api is implemented in one Controller across 5 methods. Take a look at the sole Controller `/src/Controller/RestController.php` to see how it all works.

The following requests are accepted:

**GET**: `/api/{resource}/{id}`  
**GET All**: `/api/{resource}`  
**POST**: `/api/{resource}`  
**PUT**: `/api/{resource}/{id}`  
**DELETE**: `/api/{resource}/{id}`

**Note**: The `GET all` request accepts three query parameters: `order`, `limit`, and `offset`. These are passed directly to the Doctrine `findBy()` method. [Address the Doctrine documentation for further imformation](https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/working-with-objects.html#by-simple-conditions). 

**Note**: The `POST`, `PUT`, and `DELETE` endpoints require that a valid api key is passed via the `X_AUTH_TOKEN` header. Take a look at `/src/Security/ApiTokenAuthenticator.php` and `/config/packages/security.yaml` to see how I implemented that.

## FOSHttpCacheBundle

The FOSHttpCacheBundle simplifies the process of implemnting a gateway cache in Symfony. According to the [bundle's docs](https://foshttpcachebundle.readthedocs.io/en/latest/index.html), it's features include...

- Set path-based cache expiration headers via your app configuration;
- Set up an invalidation scheme without writing PHP code;
- Tag your responses and invalidate cache based on tags;
- Send invalidation requests with minimal impact on performance;
- Differentiate caches based on user type (e.g. roles);
- Easily implement your own HTTP cache client.

I've chosen to implement the Symfony based HTTP cache. That being said, Varnish and Nginx implementations are included by the bundle. [See the documentation for more information](https://foshttpcachebundle.readthedocs.io/en/latest/index.html).

**A note on expiration**:  
Typically when configuring expiration headers, we use the `max-age` or `s-max-age` headers. The `max-age` header is honored by the browser when it caches requests. The `s-max-age` is used by reverse proxies such as Nginx, as well as cdn's like Cloudflare.

However this leads to a problem when using Symfony as a gateway cache. If I configure the Symfony gateway cache to cache an endpoint for 86400 seconds, I only want the Symfony cache to do so and not another cache implementation that may recieve the request before Symfony does. Such as an Nginx reverse proxy.

The bundle authors have thankfully addressed this issue by implementing a `reverse_proxy_ttl` header that only the Symfony gateway cache honors. 

You can of course still set, max-age, or s-max-age headers. In fact a common strategy recommended is to set the `max-age` header to a shorter length such as 500, and then the `reverse_proxy_ttl` to a longer length such as 86400. The `max-age` header allows the users own browser to cache the request during the short term and not any other cache implementations. And then the `reverse_proxy_ttl` directs the Symfony caches ttl which will be served to everyone. Remember we can't directly control the users browser cache. We can however, control and invalidate the Symfony gateway cache at will.

**Issues I Addressed**:  
A major issue that I encountered with the bundle was disabling the http cache during development. Even when disabling the http cache the 'normal' way, Symfony was still picking up the `fos_http_cache.yaml` bundle congiguration and somehow implementing certain cache features.

Here is the standard way the bundle recommends implementing the http cache:  

```php
// /public/index.php

// ...

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel = $kernel->getHttpCache();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
// ...
```

```php
// /src/Kernel.php

// ...

class Kernel extends BaseKernel implements HttpCacheProvider {
  use MicroKernelTrait;
  use HttpCacheAware;

  public function __construct(string $environment, bool $debug) {
    parent::__construct($environment, $debug);
    $this->setHttpCache(new CacheKernel($this, null, ['debug' => $debug]));
  }
```

To fix this, I decided to..

1. implement an `APP_CACHE` environment variable. When set to false, the http cache disables.
2. Remove `fos_http_cache.yaml` from `/config/packages/fos_http_cache.yaml` and move it to `/config/http_cache/fos_http_cache.yaml`.
3. In `/src/Kernel.php` and `/public/index.php` I made the following additions..

```php
// /public/index.php

// ...

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG'], (bool) $_SERVER['APP_CACHE']);

if ($_SERVER['APP_CACHE']) {
  $kernel = $kernel->getHttpCache();
}

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
// ...
```

```php
// /src/Kernel.php

class Kernel extends BaseKernel implements HttpCacheProvider {
  use MicroKernelTrait;
  use HttpCacheAware;

  // This
  private $enableCache = false;

  public function __construct(string $environment, bool $debug, /* This -> */ bool $cache = false) {
    parent::__construct($environment, $debug);

    // This
    if ($cache) {
      $this->enableCache = true;
      $this->setHttpCache(new CacheKernel($this, null, ['debug' => $debug]));
    }
  }


  protected function configureContainer(ContainerConfigurator $container): void {
    $container->import('../config/{packages}/*.yaml');
    $container->import('../config/{packages}/' . $this->environment . '/*.yaml');

    // And this 
    if ($this->enableCache) {
      $container->import('../config/http_cache/fos_http_cache.yaml');
    }
  
    if (is_file(\dirname(__DIR__) . '/config/services.yaml')) {
      $container->import('../config/{services}.yaml');
      $container->import('../config/{services}_' . $this->environment . '.yaml');
    } elseif (is_file($path = \dirname(__DIR__) . '/config/services.php')) {
      require $path($container->withPath($path), $this);
	  }
  }

// ...
```

## Additional Information

And yeah I can't think of much more to cover just now. If you run into any issues, feel free to leave an issue, and ill adress them. And yeah. I hope this example is educational, and can help as many as possible.
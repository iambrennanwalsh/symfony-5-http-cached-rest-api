<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use FOS\HttpCache\SymfonyCache\HttpCacheAware;
use FOS\HttpCache\SymfonyCache\HttpCacheProvider;

class Kernel extends BaseKernel implements HttpCacheProvider {
	use MicroKernelTrait;
	use HttpCacheAware;
	private $enableCache = false;

	public function __construct(
		string $environment,
		bool $debug,
		bool $cache = false
	) {
		parent::__construct($environment, $debug);
		if ($cache) {
			$this->enableCache = true;
			$this->setHttpCache(new CacheKernel($this, null, ['debug' => $debug]));
		}
	}

	protected function configureContainer(
		ContainerConfigurator $container
	): void {
		$container->import('../config/{packages}/*.yaml');
		$container->import(
			'../config/{packages}/' . $this->environment . '/*.yaml'
		);
		if ($this->enableCache) {
			$container->import('../config/http_cache/fos_http_cache.yaml');
		}
		if (is_file(\dirname(__DIR__) . '/config/services.yaml')) {
			$container->import('../config/{services}.yaml');
			$container->import(
				'../config/{services}_' . $this->environment . '.yaml'
			);
		} elseif (is_file($path = \dirname(__DIR__) . '/config/services.php')) {
			require $path($container->withPath($path), $this);
		}
	}

	protected function configureRoutes(RoutingConfigurator $routes): void {
		$routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
		$routes->import('../config/{routes}/*.yaml');
		if (is_file(\dirname(__DIR__) . '/config/routes.yaml')) {
			$routes->import('../config/{routes}.yaml');
		} elseif (is_file($path = \dirname(__DIR__) . '/config/routes.php')) {
			require $path($routes->withPath($path), $this);
		}
	}
}

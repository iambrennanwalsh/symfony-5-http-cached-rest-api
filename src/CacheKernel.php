<?php

namespace App;

use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\SurrogateInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\HttpCache\SymfonyCache\CacheInvalidation;
use FOS\HttpCache\SymfonyCache\EventDispatchingHttpCache;
use FOS\HttpCache\SymfonyCache\PurgeListener;
use FOS\HttpCache\SymfonyCache\PurgeTagsListener;
use FOS\HttpCache\SymfonyCache\CleanupCacheTagsListener;
use FOS\HttpCache\SymfonyCache\CustomTtlListener;
use FOS\HttpCache\SymfonyCache\UserContextListener;
use FOS\HttpCache\SymfonyCache\DebugListener;
use Toflar\Psr6HttpCacheStore\Psr6Store;
use App\Kernel;

class CacheKernel extends HttpCache implements CacheInvalidation {
	use EventDispatchingHttpCache;

	public function __construct(
		Kernel $kernel,
		SurrogateInterface $surrogate = null,
		array $options = []
	) {
		$store = new Psr6Store([
			'cache_directory' => $kernel->getCacheDir(),
			'cache_tags_header' => 'X-Cache-Tags'
		]);
		parent::__construct($kernel, $store, $surrogate, $options);
		$this->addSubscriber(new CustomTtlListener('x-reverse-proxy-ttl'));
		$this->addSubscriber(new PurgeListener());
		$this->addSubscriber(
			new PurgeTagsListener([
				'tags_header' => 'X-Cache-Tags'
			])
		);
		$this->addSubscriber(new UserContextListener());
		if (!isset($options['debug']) && !$options['debug']) {
			$this->addSubscriber(new DebugListener());
		} else {
			$this->addSubscriber(new CleanupCacheTagsListener());
		}
	}

	public function fetch(Request $request, $catch = false) {
		return parent::fetch($request, $catch);
	}
}

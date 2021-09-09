<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;

class AutoCacheControlListener {
	/**
	 * @param ResponseEvent $event
	 * @return void
	 */
	public function __invoke(ResponseEvent $event) {
		$response = $event->getResponse();
		$response->headers->set(
			AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER,
			'true'
		);
	}
}

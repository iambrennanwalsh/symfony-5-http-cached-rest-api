<?php

namespace App\Factory;

use GeneratedHydrator\GeneratedHydrator;
use GeneratedHydrator\Configuration;

/**
 * Generate a hydrator.
 */
class HydratorFactory {
	/**
	 * @param string $className
	 * @return GeneratedHydrator
	 */
	public static function factory(string $className): GeneratedHydrator {
		$config = new Configuration($className);
		return $config->createFactory()->getHydrator();
	}
}

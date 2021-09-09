<?php

namespace App\Factory;

/**
 * Generate an api token.
 */
class TokenFactory {
	/**
	 * @return string
	 */
	public static function factory(): string {
		return implode(
			'-',
			str_split(
				substr(strtolower(md5(microtime() . rand(1000, 9999))), 0, 30),
				6
			)
		);
	}
}

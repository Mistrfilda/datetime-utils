<?php

declare(strict_types=1);

namespace Mistrfilda\Datetime\Timezone;

use DateTimeZone;

class Timezone
{
	public const UTC = 'UTC';

	public const PRAGUE = 'Europe/Prague';

	public static function createTimezone(string $zone): DateTimeZone
	{
		return new DateTimeZone($zone);
	}

	public static function getUTCTimezone(): DateTimeZone
	{
		return new DateTimeZone(self::UTC);
	}

	public static function getPragueTimezone(): DateTimeZone
	{
		return new DateTimeZone(self::PRAGUE);
	}
}

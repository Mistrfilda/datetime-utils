<?php

declare(strict_types=1);

namespace Mistrfilda\Datetime\Tests\Unit\Timezone;

use Mistrfilda\Datetime\Tests\Unit\BaseUnitTest;
use Mistrfilda\Datetime\Timezone\Timezone;

class TimezoneTest extends BaseUnitTest
{
	public function testTimezones(): void
	{
		self::assertSame(
			'Europe/Prague',
			Timezone::getPragueTimezone()->getName()
		);

		self::assertSame(
			'UTC',
			Timezone::getUTCTimezone()->getName()
		);
	}
}

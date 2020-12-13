<?php

declare(strict_types=1);

namespace Mistrfilda\Datetime\Tests\Unit;

use Mistrfilda\Datetime\Tests\UpdatedTestCase;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

abstract class BaseUnitTest extends UpdatedTestCase
{
	protected DatetimeImmutable $now;

	protected function setUp(): void
	{
		parent::setUp();
		$this->now = new DateTimeImmutable();
	}
}

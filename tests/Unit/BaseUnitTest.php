<?php

declare(strict_types=1);

namespace Mistrfilda\Datetime\Tests\Unit;

use DateTimeImmutable;
use Mistrfilda\Datetime\Tests\UpdatedTestCase;

abstract class BaseUnitTest extends UpdatedTestCase
{
	protected DateTimeImmutable $now;

	protected function setUp(): void
	{
		parent::setUp();
		$this->now = new DateTimeImmutable();
	}
}

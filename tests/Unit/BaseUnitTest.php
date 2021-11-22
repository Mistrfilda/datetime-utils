<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Tests\Unit;

use Mistrfilda\Datetime\Tests\UpdatedTestCase;
use Mistrfilda\Datetime\Types\ImmutableDateTime;

abstract class BaseUnitTest extends UpdatedTestCase
{

	protected ImmutableDateTime $now;

	protected function setUp(): void
	{
		parent::setUp();
		$this->now = new ImmutableDateTime();
	}

}

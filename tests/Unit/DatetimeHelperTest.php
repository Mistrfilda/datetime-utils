<?php

declare(strict_types=1);

namespace Mistrfilda\Datetime\Tests\Unit;

use Mistrfilda\Datetime\DatetimeException;
use Mistrfilda\Datetime\DatetimeHelper;

class DatetimeHelperTest extends BaseUnitTest
{
	public function testModify(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setTime(1, 0, 0),
			DatetimeHelper::modifyDatetime($this->now->setTime(0, 0, 0), '+ 1 hour')
		);

		self::assertDatetimeImmutable(
			$this->now->setTime(1, 15, 0),
			DatetimeHelper::modifyDatetime($this->now->setTime(0, 0, 0), '+ 1 hour + 15 minutes')
		);

		self::assertException(function (): void {
			DatetimeHelper::modifyDatetime($this->now, '+ 43214zxcvz');
		}, DatetimeException::class);
	}
}

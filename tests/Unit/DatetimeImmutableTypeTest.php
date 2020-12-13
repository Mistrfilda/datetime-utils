<?php

declare(strict_types=1);

namespace Mistrfilda\Datetime\Tests\Unit;

use DateTimeImmutable as BuiltInDatetimeImmutable;
use Mistrfilda\Datetime\DatetimeException;

class DatetimeImmutableTypeTest extends BaseUnitTest
{
	public function testHelperMethods(): void
	{
		$testDatetime = new BuiltInDatetimeImmutable();
		self::assertSame(
			(int) $testDatetime->format('Y'),
			$this->now->getYear()
		);

		self::assertSame(
			(int) $testDatetime->format('m'),
			$this->now->getMonth()
		);

		self::assertSame(
			(int) $testDatetime->format('d'),
			$this->now->getDay()
		);

		self::assertSame(
			(int) $testDatetime->format('H'),
			$this->now->getHour()
		);

		self::assertSame(
			(int) $testDatetime->format('i'),
			$this->now->getMinutes()
		);

		self::assertSame(
			(int) $testDatetime->format('s'),
			$this->now->getSeconds()
		);
	}

	public function testModify(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setTime(1, 0, 0),
			$this->now->setTime(0, 0, 0)->modify('+ 1 hour')
		);

		self::assertException(function (): void {
			$this->now->modify('dasdsa');
		}, DatetimeException::class, 'Invalid modify format passed');
	}

	public function testAddHours(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setTime(3, 33, 55),
			$this->now->setTime(1, 33, 55)->addHoursToDatetime(2)
		);
	}

	public function testAddMinutes(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setTime(3, 55, 55),
			$this->now->setTime(3, 33, 55)->addMinutesToDatetime(22)
		);

		self::assertDatetimeImmutable(
			$this->now->setTime(5, 55, 55),
			$this->now->setTime(3, 33, 55)->addMinutesToDatetime(142)
		);
	}

	public function testAddSeconds(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setTime(3, 33, 55),
			$this->now->setTime(3, 32, 0)->addSecondsToDatetime(115)
		);
	}

	public function testAddDays(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth(),
				$this->now->getDay() + 5
			),
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth(),
				$this->now->getDay()
			)->addDaysToDatetime(5)
		);
	}

	public function testAddMonths(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth() + 3,
				$this->now->getDay()
			),
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth(),
				$this->now->getDay()
			)->addMonthsToDatetime(3)
		);
	}

	public function testAddYears(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setDate(
				$this->now->getYear() + 3,
				$this->now->getMonth(),
				$this->now->getDay()
			),
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth(),
				$this->now->getDay()
			)->addYearsToDatetime(3)
		);
	}
}

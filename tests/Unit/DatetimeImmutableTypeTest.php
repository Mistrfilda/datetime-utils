<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Tests\Unit;

use DateMalformedStringException;
use DateTimeImmutable as BuiltInDatetimeImmutable;

class DatetimeImmutableTypeTest extends BaseUnitTest
{

	public function testHelperMethods(): void
	{
		$testDatetime = new BuiltInDatetimeImmutable();
		self::assertSame(
			(int) $testDatetime->format('Y'),
			$this->now->getYear(),
		);

		self::assertSame(
			(int) $testDatetime->format('m'),
			$this->now->getMonth(),
		);

		self::assertSame(
			(int) $testDatetime->format('d'),
			$this->now->getDay(),
		);

		self::assertSame(
			(int) $testDatetime->format('H'),
			$this->now->getHour(),
		);

		self::assertSame(
			(int) $testDatetime->format('i'),
			$this->now->getMinutes(),
		);

		self::assertSame(
			(int) $testDatetime->format('s'),
			$this->now->getSeconds(),
		);
	}

	public function testModify(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setTime(1, 0, 0),
			$this->now->setTime(0, 0, 0)->modify('+ 1 hour'),
		);

		self::assertException(function (): void {
			$this->now->modify('dasdsa');
		},
			DateMalformedStringException::class,
			'DateTimeImmutable::modify(): Failed to parse time string (dasdsa) at position 0 (d): The timezone could not be found in the database');
	}

	public function testAddHours(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setTime(3, 33, 55),
			$this->now->setTime(1, 33, 55)->addHoursToDatetime(2),
		);

		self::assertDatetimeImmutable(
			$this->now->setTime(1, 33, 55),
			$this->now->setTime(3, 33, 55)->deductHoursFromDatetime(2),
		);
	}

	public function testAddMinutes(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setTime(3, 55, 55),
			$this->now->setTime(3, 33, 55)->addMinutesToDatetime(22),
		);

		self::assertDatetimeImmutable(
			$this->now->setTime(5, 55, 55),
			$this->now->setTime(3, 33, 55)->addMinutesToDatetime(142),
		);

		self::assertDatetimeImmutable(
			$this->now->setTime(3, 33, 55),
			$this->now->setTime(3, 55, 55)->deductMinutesFromDatetime(22),
		);
	}

	public function testAddSeconds(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setTime(3, 33, 55),
			$this->now->setTime(3, 32, 0)->addSecondsToDatetime(115),
		);

		self::assertDatetimeImmutable(
			$this->now->setTime(3, 32, 0),
			$this->now->setTime(3, 33, 55)->deductSecondsFromDatetime(115),
		);
	}

	public function testAddDays(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth(),
				$this->now->getDay() + 5,
			),
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth(),
				$this->now->getDay(),
			)->addDaysToDatetime(5),
		);

		self::assertDatetimeImmutable(
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth(),
				$this->now->getDay() - 5,
			),
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth(),
				$this->now->getDay(),
			)->deductDaysFromDatetime(5),
		);
	}

	public function testAddMonths(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth() + 3,
				$this->now->getDay(),
			),
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth(),
				$this->now->getDay(),
			)->addMonthsToDatetime(3),
		);

		self::assertDatetimeImmutable(
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth() - 3,
				$this->now->getDay(),
			),
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth(),
				$this->now->getDay(),
			)->deductMonthsFromDatetime(3),
		);
	}

	public function testAddYears(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setDate(
				$this->now->getYear() + 3,
				$this->now->getMonth(),
				$this->now->getDay(),
			),
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth(),
				$this->now->getDay(),
			)->addYearsToDatetime(3),
		);

		self::assertDatetimeImmutable(
			$this->now->setDate(
				$this->now->getYear() - 3,
				$this->now->getMonth(),
				$this->now->getDay(),
			),
			$this->now->setDate(
				$this->now->getYear(),
				$this->now->getMonth(),
				$this->now->getDay(),
			)->deductYearsFromDatetime(3),
		);
	}

}

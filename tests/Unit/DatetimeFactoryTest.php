<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Tests\Unit;

use DateTimeImmutable as BuiltInDatetimeImmutable;
use Mistrfilda\Datetime\DatetimeException;
use Mistrfilda\Datetime\DatetimeFactory;

class DatetimeFactoryTest extends BaseUnitTest
{

	private DatetimeFactory $datetimeFactory;

	public function testCreateFromFormatPreservesMicroseconds(): void
	{
		$date = DatetimeFactory::createFromFormat('2026-10-30 12:34:56.123456+02:00', 'Y-m-d H:i:s.uP');
		self::assertSame('2026-10-30 12:34:56.123456+02:00', $date->format('Y-m-d H:i:s.uP'));
	}

	public function testCreateFromFormatRejectsInvalidCalendarDates(): void
	{
		$this->expectException(DatetimeException::class);
		DatetimeFactory::createFromFormat('2026-02-30', '!Y-m-d');
	}

	public function testCreateFromFormatWrapsNullByteErrors(): void
	{
		$this->expectException(DatetimeException::class);
		DatetimeFactory::createFromFormat("2026-01-01\0", '!Y-m-d');
	}

	public function testCreateNow(): void
	{
		$before = new BuiltInDatetimeImmutable();
		$now = $this->datetimeFactory->createNow();
		$after = new BuiltInDatetimeImmutable();
		self::assertGreaterThanOrEqual($before, $now);
		self::assertLessThanOrEqual($after, $now);
	}

	public function testCreateToday(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setTime(0, 0, 0),
			$this->datetimeFactory->createToday(),
		);
	}

	public function testCreateFromTimestamp(): void
	{
		self::assertDatetimeImmutable(
			$this->now,
			$this->datetimeFactory->createFromTimestamp(
				$this->now->getTimestamp(),
				$this->now->getTimezone()->getName(),
			),
		);
	}

	public function testCreateFromMysqlFormat(): void
	{
		$testDatetime = (new BuiltInDatetimeImmutable())->setTime(23, 0, 0);

		$createdDatetime = $this->datetimeFactory->createDatetimeFromMysqlFormat(
			$testDatetime->format(DatetimeFactory::DEFAULT_MYSQL_DATETIME_FORMAT),
		);

		self::assertDatetimeImmutable($testDatetime, $createdDatetime);

		self::assertException(function () use ($testDatetime): void {
			$this->datetimeFactory->createDatetimeFromMysqlFormat(
				$testDatetime->format(DatetimeFactory::DEFAULT_MYSQL_DATETIME_FORMAT),
				'Yysf',
			);
		}, DatetimeException::class, 'Can\'t create datetime from specified value and format');
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->datetimeFactory = new DatetimeFactory();
	}

}

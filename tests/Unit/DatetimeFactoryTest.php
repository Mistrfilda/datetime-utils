<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Tests\Unit;

use DateInvalidTimeZoneException;
use DateTimeImmutable as BuiltInDatetimeImmutable;
use Mistrfilda\Datetime\DatetimeException;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Datetime\Timezone\Timezone;
use PHPUnit\Framework\Attributes\DataProvider;
use ValueError;

class DatetimeFactoryTest extends BaseUnitTest
{

	private DatetimeFactory $datetimeFactory;

	public function testCreateFromFormatPreservesMicroseconds(): void
	{
		$date = DatetimeFactory::createFromFormat('2026-10-30 12:34:56.123456+02:00', 'Y-m-d H:i:s.uP');
		self::assertSame('2026-10-30 12:34:56.123456+02:00', $date->format('Y-m-d H:i:s.uP'));
	}

	#[DataProvider('provideInvalidFormattedDates')]
	public function testCreateFromFormatRejectsInvalidCalendarDates(string $value, string $format): void
	{
		$this->expectException(DatetimeException::class);
		DatetimeFactory::createFromFormat($value, $format);
	}

	/** @return array<string, array{string, string}> */
	public static function provideInvalidFormattedDates(): array
	{
		return [
			'February overflow' => ['2026-02-30', '!Y-m-d'],
			'non-leap February' => ['2023-02-29', '!Y-m-d'],
			'non-leap century' => ['2100-02-29', '!Y-m-d'],
			'invalid month' => ['2024-13-01', '!Y-m-d'],
			'invalid hour' => ['2024-01-01 24:00:00', 'Y-m-d H:i:s'],
			'invalid minute' => ['2024-01-01 12:60:00', 'Y-m-d H:i:s'],
			'invalid second' => ['2024-01-01 12:00:60', 'Y-m-d H:i:s'],
			'trailing data' => ['2024-01-01 trailing', '!Y-m-d'],
			'empty input' => ['', '!Y-m-d'],
		];
	}

	public function testCreateFromFormatAcceptsLeapDayAndResetsUnspecifiedTime(): void
	{
		$date = DatetimeFactory::createFromFormat('2000-02-29', '!Y-m-d');
		self::assertSame('2000-02-29 00:00:00.000000', $date->format('Y-m-d H:i:s.u'));
	}

	public function testCreateFromFormatWrapsNullByteErrors(): void
	{
		try {
			DatetimeFactory::createFromFormat("2026-01-01\0", '!Y-m-d');
		} catch (DatetimeException $exception) {
			self::assertInstanceOf(ValueError::class, $exception->getPrevious());
			return;
		}

		self::fail('A null byte must raise a DatetimeException wrapping the original ValueError.');
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
		$before = new BuiltInDatetimeImmutable('today');
		$today = $this->datetimeFactory->createToday();
		$after = new BuiltInDatetimeImmutable('today');
		self::assertGreaterThanOrEqual($before, $today);
		self::assertLessThanOrEqual($after, $today);
		self::assertSame('00:00:00.000000', $today->format('H:i:s.u'));
	}

	#[DataProvider('provideTimestamps')]
	public function testCreateFromTimestamp(int $timestamp, string $timezone, string $expected): void
	{
		$date = $this->datetimeFactory->createFromTimestamp($timestamp, $timezone);
		self::assertSame($expected, $date->format('Y-m-d H:i:s.u P'));
		self::assertSame($timestamp, $date->getTimestamp());
		self::assertSame($timezone, $date->getTimezone()->getName());
	}

	/** @return array<string, array{int, string, string}> */
	public static function provideTimestamps(): array
	{
		return [
			'Unix epoch' => [0, Timezone::UTC, '1970-01-01 00:00:00.000000 +00:00'],
			'before Unix epoch' => [-1, Timezone::UTC, '1969-12-31 23:59:59.000000 +00:00'],
			'Unix epoch in Prague' => [0, Timezone::PRAGUE, '1970-01-01 01:00:00.000000 +01:00'],
			'before Unix epoch in Prague' => [-1, Timezone::PRAGUE, '1970-01-01 00:59:59.000000 +01:00'],
			'after spring clock change' => [1711848600, Timezone::PRAGUE, '2024-03-31 03:30:00.000000 +02:00'],
			'after signed 32-bit range' => [2147483648, Timezone::UTC, '2038-01-19 03:14:08.000000 +00:00'],
		];
	}

	public function testTimestampDefaultsToUtcRegardlessOfDefaultTimezone(): void
	{
		$originalTimezone = date_default_timezone_get();
		try {
			date_default_timezone_set(Timezone::PRAGUE);
			$date = $this->datetimeFactory->createFromTimestamp(0);
			self::assertSame('1970-01-01 00:00:00 UTC', $date->format('Y-m-d H:i:s e'));
		} finally {
			date_default_timezone_set($originalTimezone);
		}
	}

	public function testTimestampWrapsInvalidTimezoneErrors(): void
	{
		try {
			$this->datetimeFactory->createFromTimestamp(0, 'Invalid/Timezone');
		} catch (DatetimeException $exception) {
			self::assertInstanceOf(DateInvalidTimeZoneException::class, $exception->getPrevious());
			self::assertStringContainsString('Invalid/Timezone', $exception->getMessage());
			return;
		}

		self::fail('An invalid timezone must raise a DatetimeException wrapping the original exception.');
	}

	public function testCreateFromMysqlFormat(): void
	{
		$testDatetime = new BuiltInDatetimeImmutable('2024-02-29 23:00:00');

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

	public function testMysqlFormatRejectsInvalidCalendarDate(): void
	{
		$this->expectException(DatetimeException::class);
		$this->datetimeFactory->createDatetimeFromMysqlFormat('2023-02-29 12:34:56');
	}

	public function testMysqlCustomFormatPreservesMicrosecondsAndOffset(): void
	{
		$date = $this->datetimeFactory->createDatetimeFromMysqlFormat(
			'2024-02-29 12:34:56.654321+01:00',
			'Y-m-d H:i:s.uP',
		);
		self::assertSame('2024-02-29 12:34:56.654321+01:00', $date->format('Y-m-d H:i:s.uP'));
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->datetimeFactory = new DatetimeFactory();
	}

}

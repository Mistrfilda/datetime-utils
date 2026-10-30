<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Tests\Unit;

use DateMalformedStringException;
use Mistrfilda\Datetime\Timezone\Timezone;
use Mistrfilda\Datetime\Types\ImmutableDateTime;
use PHPUnit\Framework\Attributes\DataProvider;

class DatetimeImmutableTypeTest extends BaseUnitTest
{

	public function testHelperMethods(): void
	{
		$date = new ImmutableDateTime('2024-02-29 23:45:12.123456', Timezone::getPragueTimezone());
		self::assertSame(2024, $date->getYear());
		self::assertSame(2, $date->getMonth());
		self::assertSame(29, $date->getDay());
		self::assertSame(23, $date->getHour());
		self::assertSame(45, $date->getMinutes());
		self::assertSame(12, $date->getSeconds());
	}

	public function testModify(): void
	{
		$date = new ImmutableDateTime('2023-12-31 23:00:00');
		self::assertDatetimeImmutable(new ImmutableDateTime('2024-01-01 00:00:00'), $date->modify('+ 1 hour'));
		$this->expectException(DateMalformedStringException::class);
		$date->modify('dasdsa');
	}

	#[DataProvider('provideHourOffsets')]
	public function testAddHours(int $amount, string $expectedAdd, string $expectedDeduct): void
	{
		$date = new ImmutableDateTime('2024-01-01 00:00:00');
		self::assertDatetimeImmutable(new ImmutableDateTime($expectedAdd), $date->addHoursToDatetime($amount));
		self::assertDatetimeImmutable(new ImmutableDateTime($expectedDeduct), $date->deductHoursFromDatetime($amount));
		self::assertSame('2024-01-01 00:00:00', $date->format('Y-m-d H:i:s'));
	}

	/** @return array<string, array{int, string, string}> */
	public static function provideHourOffsets(): array
	{
		return [
			'across year boundary' => [2, '2024-01-01 02:00:00', '2023-12-31 22:00:00'],
			'zero' => [0, '2024-01-01 00:00:00', '2024-01-01 00:00:00'],
			'negative' => [-2, '2023-12-31 22:00:00', '2024-01-01 02:00:00'],
		];
	}

	#[DataProvider('provideMinuteOffsets')]
	public function testAddMinutes(int $amount, string $expectedAdd, string $expectedDeduct): void
	{
		$date = new ImmutableDateTime('2024-01-01 00:00:00');
		self::assertDatetimeImmutable(new ImmutableDateTime($expectedAdd), $date->addMinutesToDatetime($amount));
		self::assertDatetimeImmutable(
			new ImmutableDateTime($expectedDeduct),
			$date->deductMinutesFromDatetime($amount),
		);
		self::assertSame('2024-01-01 00:00:00', $date->format('Y-m-d H:i:s'));
	}

	/** @return array<string, array{int, string, string}> */
	public static function provideMinuteOffsets(): array
	{
		return [
			'across hour and year boundaries' => [90, '2024-01-01 01:30:00', '2023-12-31 22:30:00'],
			'zero' => [0, '2024-01-01 00:00:00', '2024-01-01 00:00:00'],
			'negative' => [-90, '2023-12-31 22:30:00', '2024-01-01 01:30:00'],
		];
	}

	#[DataProvider('provideSecondOffsets')]
	public function testAddSeconds(int $amount, string $expectedAdd, string $expectedDeduct): void
	{
		$date = new ImmutableDateTime('2024-01-01 00:00:00');
		self::assertDatetimeImmutable(new ImmutableDateTime($expectedAdd), $date->addSecondsToDatetime($amount));
		self::assertDatetimeImmutable(
			new ImmutableDateTime($expectedDeduct),
			$date->deductSecondsFromDatetime($amount),
		);
		self::assertSame('2024-01-01 00:00:00', $date->format('Y-m-d H:i:s'));
	}

	/** @return array<string, array{int, string, string}> */
	public static function provideSecondOffsets(): array
	{
		return [
			'across minute and year boundaries' => [115, '2024-01-01 00:01:55', '2023-12-31 23:58:05'],
			'zero' => [0, '2024-01-01 00:00:00', '2024-01-01 00:00:00'],
			'negative' => [-115, '2023-12-31 23:58:05', '2024-01-01 00:01:55'],
		];
	}

	#[DataProvider('provideDayOffsets')]
	public function testAddDays(string $start, int $amount, string $expectedAdd, string $expectedDeduct): void
	{
		$date = new ImmutableDateTime($start);
		self::assertDatetimeImmutable(new ImmutableDateTime($expectedAdd), $date->addDaysToDatetime($amount));
		self::assertDatetimeImmutable(new ImmutableDateTime($expectedDeduct), $date->deductDaysFromDatetime($amount));
		self::assertSame($start, $date->format('Y-m-d'));
	}

	/** @return array<string, array{string, int, string, string}> */
	public static function provideDayOffsets(): array
	{
		return [
			'year boundary' => ['2024-01-01', 1, '2024-01-02', '2023-12-31'],
			'leap year' => ['2024-03-01', 1, '2024-03-02', '2024-02-29'],
			'non-leap year' => ['2023-03-01', 1, '2023-03-02', '2023-02-28'],
			'zero' => ['2024-01-01', 0, '2024-01-01', '2024-01-01'],
			'negative' => ['2024-01-01', -1, '2023-12-31', '2024-01-02'],
		];
	}

	#[DataProvider('provideMonthOffsets')]
	public function testAddMonths(string $start, int $amount, string $expectedAdd, string $expectedDeduct): void
	{
		$date = new ImmutableDateTime($start);
		self::assertDatetimeImmutable(new ImmutableDateTime($expectedAdd), $date->addMonthsToDatetime($amount));
		self::assertDatetimeImmutable(new ImmutableDateTime($expectedDeduct), $date->deductMonthsFromDatetime($amount));
		self::assertSame($start, $date->format('Y-m-d'));
	}

	/** @return array<string, array{string, int, string, string}> */
	public static function provideMonthOffsets(): array
	{
		return [
			'year boundary' => ['2024-01-01', 2, '2024-03-01', '2023-11-01'],
			'leap February overflow' => ['2024-01-31', 1, '2024-03-02', '2023-12-31'],
			'non-leap February overflow' => ['2023-01-31', 1, '2023-03-03', '2022-12-31'],
			'subtraction overflow' => ['2024-03-31', 1, '2024-05-01', '2024-03-02'],
			'zero' => ['2024-01-31', 0, '2024-01-31', '2024-01-31'],
			'negative' => ['2024-01-01', -2, '2023-11-01', '2024-03-01'],
		];
	}

	#[DataProvider('provideYearOffsets')]
	public function testAddYears(string $start, int $amount, string $expectedAdd, string $expectedDeduct): void
	{
		$date = new ImmutableDateTime($start);
		self::assertDatetimeImmutable(new ImmutableDateTime($expectedAdd), $date->addYearsToDatetime($amount));
		self::assertDatetimeImmutable(new ImmutableDateTime($expectedDeduct), $date->deductYearsFromDatetime($amount));
		self::assertSame($start, $date->format('Y-m-d'));
	}

	/** @return array<string, array{string, int, string, string}> */
	public static function provideYearOffsets(): array
	{
		return [
			'positive' => ['2024-01-01', 2, '2026-01-01', '2022-01-01'],
			'leap day overflow' => ['2024-02-29', 1, '2025-03-01', '2023-03-01'],
			'century is not a leap year' => ['2000-02-29', 100, '2100-03-01', '1900-03-01'],
			'zero' => ['2024-02-29', 0, '2024-02-29', '2024-02-29'],
			'negative' => ['2024-01-01', -2, '2022-01-01', '2026-01-01'],
		];
	}

	public function testAddToDatetime(): void
	{
		$date = new ImmutableDateTime('2023-12-31 23:45:12.123456', Timezone::getPragueTimezone());
		self::assertSame(
			'2025-02-03 02:15:12.123456 Europe/Prague',
			$date->addToDatetime(hours: 2, minutes: 30, days: 2, months: 1, years: 1)->format('Y-m-d H:i:s.u e'),
		);
		self::assertSame(
			'2022-11-29 21:15:12.123456 Europe/Prague',
			$date->addToDatetime(hours: -2, minutes: -30, days: -2, months: -1, years: -1)->format('Y-m-d H:i:s.u e'),
		);
		self::assertSame(
			'2024-01-01 00:15:12.123456 Europe/Prague',
			$date->addToDatetime(minutes: 30)->format('Y-m-d H:i:s.u e'),
		);
		self::assertSame(
			'2024-12-03 22:15:12.123456 Europe/Prague',
			$date->addToDatetime(hours: -2, minutes: 30, days: 2, months: -1, years: 1)->format('Y-m-d H:i:s.u e'),
		);
		$unchanged = $date->addToDatetime();
		self::assertNotSame($date, $unchanged);
		self::assertDatetimeImmutable($date, $unchanged);
		self::assertSame('2023-12-31 23:45:12.123456 Europe/Prague', $date->format('Y-m-d H:i:s.u e'));
	}

	public function testArithmeticPreservesMicrosecondsAndTimezone(): void
	{
		$date = new ImmutableDateTime('2024-01-15 12:34:56.123456', Timezone::getPragueTimezone());
		$results = [
			$date->modify('+ 1 day'),
			$date->addHoursToDatetime(1),
			$date->deductHoursFromDatetime(1),
			$date->addMinutesToDatetime(1),
			$date->deductMinutesFromDatetime(1),
			$date->addSecondsToDatetime(1),
			$date->deductSecondsFromDatetime(1),
			$date->addDaysToDatetime(1),
			$date->deductDaysFromDatetime(1),
			$date->addMonthsToDatetime(1),
			$date->deductMonthsFromDatetime(1),
			$date->addYearsToDatetime(1),
			$date->deductYearsFromDatetime(1),
		];
		foreach ($results as $result) {
			self::assertNotSame($date, $result);
			self::assertSame('123456', $result->format('u'));
			self::assertSame(Timezone::PRAGUE, $result->getTimezone()->getName());
		}

		self::assertSame('2024-01-15 12:34:56.123456 Europe/Prague', $date->format('Y-m-d H:i:s.u e'));
	}

	#[DataProvider('provideDaylightSavingTransitions')]
	public function testDayArithmeticAcrossDaylightSavingTime(
		string $start,
		string $expected,
		int $elapsedSeconds,
	): void
	{
		$date = new ImmutableDateTime($start, Timezone::getPragueTimezone());
		$nextDay = $date->addDaysToDatetime(1);
		self::assertSame($expected, $nextDay->format('Y-m-d H:i:s P'));
		self::assertSame($elapsedSeconds, $nextDay->getTimestamp() - $date->getTimestamp());
		self::assertDatetimeImmutable($date, $nextDay->deductDaysFromDatetime(1));
	}

	/** @return array<string, array{string, string, int}> */
	public static function provideDaylightSavingTransitions(): array
	{
		return [
			'spring day has 23 hours' => ['2024-03-30 12:00:00', '2024-03-31 12:00:00 +02:00', 82800],
			'autumn day has 25 hours' => ['2024-10-26 12:00:00', '2024-10-27 12:00:00 +01:00', 90000],
		];
	}

	public function testHourArithmeticSkipsNonexistentLocalTime(): void
	{
		$date = new ImmutableDateTime('2024-03-31 01:30:00', Timezone::getPragueTimezone());
		$nextHour = $date->addHoursToDatetime(1);
		self::assertSame('2024-03-31 03:30:00 +02:00', $nextHour->format('Y-m-d H:i:s P'));
		self::assertSame(3600, $nextHour->getTimestamp() - $date->getTimestamp());
	}

	#[DataProvider('provideWeekdays')]
	public function testIsWeekend(string $date, bool $expected): void
	{
		self::assertSame($expected, new ImmutableDateTime($date)->isWeekend());
	}

	/** @return array<string, array{string, bool}> */
	public static function provideWeekdays(): array
	{
		return [
			'Monday' => ['2024-01-01', false],
			'Tuesday' => ['2024-01-02', false],
			'Wednesday' => ['2024-01-03', false],
			'Thursday' => ['2024-01-04', false],
			'Friday' => ['2024-01-05', false],
			'Saturday' => ['2024-01-06', true],
			'Sunday' => ['2024-01-07', true],
		];
	}

	public function testWeekendUsesLocalDate(): void
	{
		$friday = new ImmutableDateTime('2024-01-05 23:30:00', Timezone::getUTCTimezone());
		$sunday = new ImmutableDateTime('2024-01-07 23:30:00', Timezone::getUTCTimezone());
		self::assertFalse($friday->isWeekend());
		self::assertTrue($friday->setTimezone(Timezone::getPragueTimezone())->isWeekend());
		self::assertTrue($sunday->isWeekend());
		self::assertFalse($sunday->setTimezone(Timezone::getPragueTimezone())->isWeekend());
	}

}

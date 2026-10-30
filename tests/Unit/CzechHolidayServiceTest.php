<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Tests\Unit;

use DateTimeImmutable;
use DateTimeZone;
use Mistrfilda\Datetime\Holiday\CzechHolidayService;
use PHPUnit\Framework\Attributes\DataProvider;
use function count;
use function current;
use const PHP_INT_MAX;

class CzechHolidayServiceTest extends BaseUnitTest
{

	private CzechHolidayService $czechHolidayService;

	public function testHolidaysList(): void
	{
		self::assertCount(13, $this->czechHolidayService->getYearHolidays(2026));
		self::assertCount(13, $this->czechHolidayService->getYearHolidays(2025));
	}

	public function testHolidaysAfter2030(): void
	{
		self::assertCount(13, $this->czechHolidayService->getYearHolidays(2031));
		self::assertTrue($this->czechHolidayService->isDateHoliday(11, 4, 2031));
		self::assertTrue($this->czechHolidayService->isDateHoliday(14, 4, 2031));
		self::assertTrue($this->czechHolidayService->isDateHoliday(24, 12, 2031));
	}

	public function testLookupUsesCalendarDateInTheInputTimezone(): void
	{
		foreach (['UTC', 'Europe/Prague', 'America/New_York', 'Pacific/Kiritimati'] as $timezone) {
			$date = new DateTimeImmutable('2026-12-24 00:30:00.123456', new DateTimeZone($timezone));
			self::assertTrue($this->czechHolidayService->isDateTimeHoliday($date), $timezone);
			$holiday = $this->czechHolidayService->getCzechHolidayByDatetime($date);
			self::assertNotNull($holiday);
			self::assertSame('Štědrý den', $holiday->getName());
			self::assertFalse($this->czechHolidayService->isDateTimeHoliday($date->modify('-1 day')));
		}
	}

	public function testInvalidDatesAreNotNormalizedToHolidays(): void
	{
		foreach ([[31, 4, 2026], [0, 5, 2026], [29, 2, 2025], [1, 13, 2026]] as [$day, $month, $year]) {
			self::assertFalse($this->czechHolidayService->isDateHoliday($day, $month, $year));
			self::assertNull($this->czechHolidayService->getCzechHolidayByDayMonthYear($day, $month, $year));
		}
	}

	/**
	 * @return array<int, array{int, string, string}>
	 */
	public static function provideEasterDates(): array
	{
		return [
			[2020, '04-10', '04-13'],
			[2021, '04-02', '04-05'],
			[2022, '04-15', '04-18'],
			[2023, '04-07', '04-10'],
			[2024, '03-29', '04-01'],
			[2025, '04-18', '04-21'],
			[2026, '04-03', '04-06'],
			[2027, '03-26', '03-29'],
			[2028, '04-14', '04-17'],
			[2029, '03-30', '04-02'],
			[2030, '04-19', '04-22'],
			[2031, '04-11', '04-14'],
			[2038, '04-23', '04-26'],
			[2049, '04-16', '04-19'],
			[2100, '03-26', '03-29'],
		];
	}

	#[DataProvider('provideEasterDates')]
	public function testCompleteHolidayCalendar(int $year, string $goodFriday, string $easterMonday): void
	{
		$service = new CzechHolidayService(new DateTimeZone('Europe/Prague'));
		$holidays = $service->getYearHolidays($year);
		$dates = [];
		foreach ($holidays as $timestamp => $holiday) {
			self::assertSame($timestamp, $holiday->getTimestamp());
			self::assertSame((string) $year, $holiday->getDate()->format('Y'));
			self::assertSame('00:00:00.000000', $holiday->getDate()->format('H:i:s.u'));
			self::assertSame('Europe/Prague', $holiday->getDate()->getTimezone()->getName());
			self::assertSame($holiday, $service->getCzechHolidayByDatetime($holiday->getDate()));
			$dates[$holiday->getDate()->format('m-d')] = $holiday->getName();
		}

		self::assertSame([
			'01-01' => 'Den obnovy samostatného českého státu',
			$goodFriday => 'Velký pátek',
			$easterMonday => 'Velikonoční pondělí',
			'05-01' => 'Svátek práce',
			'05-08' => 'Den vítězství',
			'07-05' => 'Den slovanských věrozvěstů Cyrila a Metoděje',
			'07-06' => 'Den upálení mistra Jana Husa',
			'09-28' => 'Den české státnosti',
			'10-28' => 'Den vzniku samostatného československého státu',
			'11-17' => 'Den boje za svobodu a demokracii',
			'12-24' => 'Štědrý den',
			'12-25' => '1. svátek vánoční',
			'12-26' => '2. svátek vánoční',
		], $dates);
		self::assertSame($holidays, $service->getYearHolidays($year));

		$monday = new DateTimeImmutable($year . '-' . $easterMonday, new DateTimeZone('Europe/Prague'));
		self::assertFalse($service->isDateTimeHoliday($monday->modify('-1 day')));
		self::assertFalse($service->isDateTimeHoliday($monday->modify('+1 day')));
	}

	public function testUnsupportedYears(): void
	{
		foreach ([-1, 0, 2019, 32768, PHP_INT_MAX] as $year) {
			self::assertSame([], $this->czechHolidayService->getYearHolidays($year));
			self::assertFalse($this->czechHolidayService->isDateHoliday(1, 1, $year));
			self::assertNull($this->czechHolidayService->getCzechHolidayByDayMonthYear(1, 1, $year));
		}
	}

	public function testTimezoneIsStableWhenTheDefaultChanges(): void
	{
		$originalTimezone = date_default_timezone_get();
		try {
			date_default_timezone_set('Europe/Prague');
			$service = new CzechHolidayService();
			$cachedHolidays = $service->getYearHolidays(2026);
			date_default_timezone_set('America/New_York');
			self::assertSame($cachedHolidays, $service->getYearHolidays(2026));
			self::assertTrue($service->isDateHoliday(24, 12, 2026));
			foreach ($service->getYearHolidays(2027) as $holiday) {
				self::assertSame('Europe/Prague', $holiday->getDate()->getTimezone()->getName());
				self::assertTrue($service->isDateTimeHoliday($holiday->getDate()));
			}
		} finally {
			date_default_timezone_set($originalTimezone);
		}
	}

	public function testEasterAcrossDaylightSavingChange(): void
	{
		$service = new CzechHolidayService(new DateTimeZone('Europe/Prague'));
		$friday = $service->getCzechHolidayByDayMonthYear(29, 3, 2024);
		$monday = $service->getCzechHolidayByDayMonthYear(1, 4, 2024);
		self::assertNotNull($friday);
		self::assertNotNull($monday);
		self::assertSame('00:00:00 +01:00', $friday->getDate()->format('H:i:s P'));
		self::assertSame('00:00:00 +02:00', $monday->getDate()->format('H:i:s P'));
	}

	public function testCurrentYearHolidays(): void
	{
		$currentYearsHolidays = $this->czechHolidayService->getCurrentYearHolidays();

		self::assertCount(
			count($this->czechHolidayService->getYearHolidays($this->now->getYear())),
			$currentYearsHolidays,
		);

		$currentElement = current($currentYearsHolidays);
		self::assertNotFalse($currentElement);

		self::assertSame(
			(int) $currentElement->getDate()->format('Y'),
			$this->now->getYear(),
		);
	}

	public function testGetterMethods(): void
	{
		self::assertFalse(
			$this->czechHolidayService->isDateTimeHoliday(
				$this->now->setDate(2020, 2, 12),
			),
		);

		self::assertTrue(
			$this->czechHolidayService->isDateTimeHoliday(
				$this->now->setDate(2020, 12, 24),
			),
		);

		self::assertTrue(
			$this->czechHolidayService->isDateTimeHoliday(
				$this->now->setDate(2026, 4, 6),
			),
		);

		self::assertFalse(
			$this->czechHolidayService->isDateTimeHoliday(
				$this->now->setDate(2020, 4, 5),
			),
		);

		self::assertTrue(
			$this->czechHolidayService->isDateHoliday(24, 12, 2021),
		);

		self::assertFalse(
			$this->czechHolidayService->isDateHoliday(5, 3, 2022),
		);

		self::assertNotNull(
			$this->czechHolidayService->getCzechHolidayByDatetime(
				$this->now->setDate(2025, 1, 1),
			),
		);

		self::assertNull(
			$this->czechHolidayService->getCzechHolidayByDatetime(
				$this->now->setDate(2025, 1, 2),
			),
		);

		self::assertNull(
			$this->czechHolidayService->getCzechHolidayByDayMonthYear(
				8,
				3,
				2021,
			),
		);

		self::assertNotNull(
			$this->czechHolidayService->getCzechHolidayByDayMonthYear(
				24,
				12,
				2021,
			),
		);
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->czechHolidayService = new CzechHolidayService();
	}

}

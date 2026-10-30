<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Holiday;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use const CAL_EASTER_ALWAYS_GREGORIAN;

class CzechHolidayService
{

	private const array FIXED_HOLIDAYS = [
		'Den obnovy samostatného českého státu' => [1, 1],
		'Svátek práce' => [1, 5],
		'Den vítězství' => [8, 5],
		'Den slovanských věrozvěstů Cyrila a Metoděje' => [5, 7],
		'Den upálení mistra Jana Husa' => [6, 7],
		'Den české státnosti' => [28, 9],
		'Den vzniku samostatného československého státu' => [28, 10],
		'Den boje za svobodu a demokracii' => [17, 11],
		'Štědrý den' => [24, 12],
		'1. svátek vánoční' => [25, 12],
		'2. svátek vánoční' => [26, 12],
	];

	/** @var array<int, array<int, CzechHoliday>> */
	private array $processedHolidays = [];

	private readonly DateTimeZone $timezone;

	public function __construct(DateTimeZone|null $timezone = null)
	{
		$this->timezone = $timezone ?? new DateTimeZone(date_default_timezone_get());
	}

	/**
	 * @return array<int, CzechHoliday>
	 */
	public function getCurrentYearHolidays(): array
	{
		return $this->getYearHolidays((int) (new DateTimeImmutable('now', $this->timezone))->format('Y'));
	}

	/**
	 * @return array<int, CzechHoliday>
	 */
	public function getYearHolidays(int $year): array
	{
		// Preserve the original lower bound; historical holiday rules are not modelled.
		if ($year < 2020 || checkdate(1, 1, $year) === false) {
			return [];
		}

		if (isset($this->processedHolidays[$year])) {
			return $this->processedHolidays[$year];
		}

		$holidays = [];
		foreach (self::FIXED_HOLIDAYS as $name => [$day, $month]) {
			$holiday = new CzechHoliday($name, $this->createDate($day, $month, $year));
			$holidays[$holiday->getTimestamp()] = $holiday;
		}

		// Calendar-day arithmetic preserves local midnight across daylight-saving changes.
		$easterSunday = $this->createDate(21, 3, $year)->add(
			new DateInterval('P' . easter_days($year, CAL_EASTER_ALWAYS_GREGORIAN) . 'D'),
		);
		foreach ([
			new CzechHoliday('Velký pátek', $easterSunday->modify('-2 days')),
			new CzechHoliday('Velikonoční pondělí', $easterSunday->modify('+1 day')),
		] as $holiday) {
			$holidays[$holiday->getTimestamp()] = $holiday;
		}

		ksort($holidays);
		$this->processedHolidays[$year] = $holidays;

		return $holidays;
	}

	public function isDateTimeHoliday(DateTimeImmutable $date): bool
	{
		return $this->getCzechHolidayByDatetime($date) !== null;
	}

	public function isDateHoliday(int $day, int $month, int $year): bool
	{
		return $this->getCzechHolidayByDayMonthYear($day, $month, $year) !== null;
	}

	public function getCzechHolidayByDatetime(DateTimeImmutable $date): CzechHoliday|null
	{
		return $this->getCzechHolidayByDayMonthYear(
			(int) $date->format('j'),
			(int) $date->format('n'),
			(int) $date->format('Y'),
		);
	}

	public function getCzechHolidayByDayMonthYear(int $day, int $month, int $year): CzechHoliday|null
	{
		if (checkdate($month, $day, $year) === false) {
			return null;
		}

		$timestamp = $this->createDate($day, $month, $year)->getTimestamp();

		return $this->getYearHolidays($year)[$timestamp] ?? null;
	}

	private function createDate(int $day, int $month, int $year): DateTimeImmutable
	{
		return (new DateTimeImmutable('now', $this->timezone))->setDate($year, $month, $day)->setTime(0, 0);
	}

}

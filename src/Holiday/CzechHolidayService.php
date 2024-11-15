<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Holiday;

use DateTimeImmutable;

class CzechHolidayService
{

	/**
	 * List of all czech holidays, in format year => name => day, month
	 */
	private const DATA = [
		2020 => [
			'Den obnovy samostatného českého státu' => [1, 1],
			'Velký pátek' => [10, 4],
			'Velikonoční pondělí' => [13, 4],
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
		],
		2021 => [
			'Den obnovy samostatného českého státu' => [1, 1],
			'Velký pátek' => [2, 4],
			'Velikonoční pondělí' => [5, 4],
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
		],
		2022 => [
			'Den obnovy samostatného českého státu' => [1, 1],
			'Velký pátek' => [15, 4],
			'Velikonoční pondělí' => [18, 4],
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
		],
		2023 => [
			'Den obnovy samostatného českého státu' => [1, 1],
			'Velký pátek' => [7, 4],
			'Velikonoční pondělí' => [10, 4],
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
		],
		2024 => [
			'Den obnovy samostatného českého státu' => [1, 1],
			'Velký pátek' => [29, 3],
			'Velikonoční pondělí' => [1, 4],
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
		],
		2025 => [
			'Den obnovy samostatného českého státu' => [1, 1],
			'Velký pátek' => [18, 4],
			'Velikonoční pondělí' => [21, 4],
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
		],
		2026 => [
			'Den obnovy samostatného českého státu' => [1, 1],
			'Velký pátek' => [3, 4],
			'Velikonoční pondělí' => [6, 4],
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
		],
		2027 => [
			'Den obnovy samostatného českého státu' => [1, 1],
			'Velký pátek' => [26, 3],
			'Velikonoční pondělí' => [29, 3],
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
		],
		2028 => [
			'Den obnovy samostatného českého státu' => [1, 1],
			'Velký pátek' => [14, 4],
			'Velikonoční pondělí' => [17, 4],
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
		],
		2029 => [
			'Den obnovy samostatného českého státu' => [1, 1],
			'Velký pátek' => [30, 3],
			'Velikonoční pondělí' => [2, 4],
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
		],
		2030 => [
			'Den obnovy samostatného českého státu' => [1, 1],
			'Velký pátek' => [19, 4],
			'Velikonoční pondělí' => [22, 4],
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
		],
	];

	/** @var array<int, CzechHoliday> */
	private array $currentYearHolidays = [];

	/**
	 * Simple cache
	 *
	 * @var array<int, array<int, CzechHoliday>>
	 */
	private array $proccessedHolidays = [];

	public function __construct()
	{
		$currentYear = (int) (new DateTimeImmutable())->format('Y');
		$this->currentYearHolidays = $this->getHolidays($currentYear);
		$this->proccessedHolidays[$currentYear] = $this->currentYearHolidays;
	}

	/**
	 * @return array<int, CzechHoliday>
	 */
	public function getCurrentYearHolidays(): array
	{
		return $this->currentYearHolidays;
	}

	/**
	 * @return array<int, CzechHoliday>
	 */
	public function getYearHolidays(int $year): array
	{
		if (array_key_exists($year, $this->proccessedHolidays)) {
			return $this->proccessedHolidays[$year];
		}

		$parsedHolidays = $this->getHolidays($year);
		$this->proccessedHolidays[$year] = $parsedHolidays;

		return $parsedHolidays;
	}

	public function isDateTimeHoliday(DateTimeImmutable $date): bool
	{
		$parsedDate = $date->setTime(0, 0, 0, 0);

		return $this->checkDate($parsedDate, (int) $parsedDate->format('Y'));
	}

	public function isDateHoliday(int $day, int $month, int $year): bool
	{
		$parsedDate = (new DateTimeImmutable())
			->setTime(0, 0, 0, 0)
			->setDate($year, $month, $day);

		return $this->checkDate($parsedDate, $year);
	}

	public function getCzechHolidayByDatetime(DateTimeImmutable $date): CzechHoliday|null
	{
		$parsedDate = $date->setTime(0, 0, 0, 0);

		return $this->getCzechHoliday($parsedDate, (int) $parsedDate->format('Y'));
	}

	public function getCzechHolidayByDayMonthYear(int $day, int $month, int $year): CzechHoliday|null
	{
		$parsedDate = (new DateTimeImmutable())
			->setTime(0, 0, 0, 0)
			->setDate($year, $month, $day);

		return $this->getCzechHoliday($parsedDate, $year);
	}

	private function getCzechHoliday(DateTimeImmutable $parsedDate, int $year): CzechHoliday|null
	{
		$holidays = $this->getHolidays($year);
		if (array_key_exists($parsedDate->getTimestamp(), $holidays)) {
			return $holidays[$parsedDate->getTimestamp()];
		}

		return null;
	}

	private function checkDate(DateTimeImmutable $parsedDate, int $year): bool
	{
		if (array_key_exists($parsedDate->getTimestamp(), $this->getHolidays($year))) {
			return true;
		}

		return false;
	}

	/**
	 * @return array<int, CzechHoliday>
	 */
	private function getHolidays(int $year): array
	{
		$now = new DateTimeImmutable();

		if (array_key_exists($year, self::DATA) === false) {
			return [];
		}

		if (array_key_exists($year, $this->proccessedHolidays)) {
			return $this->proccessedHolidays[$year];
		}

		$holidays = [];
		foreach (self::DATA[$year] as $name => $dates) {
			$czechHoliday = new CzechHoliday(
				$name,
				$now->setDate($year, $dates[1], $dates[0]),
			);

			$holidays[$czechHoliday->getTimestamp()] = $czechHoliday;
		}

		$this->proccessedHolidays[$year] = $holidays;

		return $holidays;
	}

}

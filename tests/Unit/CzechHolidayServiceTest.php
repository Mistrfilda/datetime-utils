<?php

declare(strict_types=1);

namespace Mistrfilda\Datetime\Tests\Unit;

use Mistrfilda\Datetime\Holiday\CzechHolidayService;

class CzechHolidayServiceTest extends BaseUnitTest
{
	private CzechHolidayService $czechHolidayService;

	public function testHolidaysList(): void
	{
		self::assertCount(13, $this->czechHolidayService->getYearHolidays(2026));
		self::assertCount(13, $this->czechHolidayService->getYearHolidays(2025));
	}

	public function testCurrentYearHolidays(): void
	{
		$currentYearsHolidays = $this->czechHolidayService->getCurrentYearHolidays();

		self::assertCount(
			count($this->czechHolidayService->getYearHolidays($this->now->getYear())),
			$currentYearsHolidays
		);

		$currentElement = current($currentYearsHolidays);
		self::assertNotFalse($currentElement);

		self::assertSame(
			(int) $currentElement->getDate()->format('Y'),
			$this->now->getYear()
		);
	}

	public function testGetterMethods(): void
	{
		self::assertFalse(
			$this->czechHolidayService->isDateTimeHoliday(
				$this->now->setDate(2020, 2, 12)
			)
		);

		self::assertTrue(
			$this->czechHolidayService->isDateTimeHoliday(
				$this->now->setDate(2020, 12, 24)
			)
		);

		self::assertTrue(
			$this->czechHolidayService->isDateTimeHoliday(
				$this->now->setDate(2026, 4, 6)
			)
		);

		self::assertFalse(
			$this->czechHolidayService->isDateTimeHoliday(
				$this->now->setDate(2020, 4, 5)
			)
		);

		self::assertTrue(
			$this->czechHolidayService->isDateHoliday(24, 12, 2021)
		);

		self::assertFalse(
			$this->czechHolidayService->isDateHoliday(5, 3, 2022)
		);

		self::assertNotNull(
			$this->czechHolidayService->getCzechHolidayByDatetime(
				$this->now->setDate(2025, 1, 1)
			)
		);

		self::assertNull(
			$this->czechHolidayService->getCzechHolidayByDatetime(
				$this->now->setDate(2025, 1, 2)
			)
		);

		self::assertNull(
			$this->czechHolidayService->getCzechHolidayByDayMonthYear(
				8, 3, 2021
			)
		);

		self::assertNotNull(
			$this->czechHolidayService->getCzechHolidayByDayMonthYear(
				24, 12, 2021
			)
		);
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->czechHolidayService = new CzechHolidayService();
	}
}

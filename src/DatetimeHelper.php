<?php

declare(strict_types=1);

namespace Mistrfilda\Datetime;

use DateTimeImmutable;

class DatetimeHelper
{
	public static function modifyDatetime(DateTimeImmutable $date, string $modifyFormat): DateTimeImmutable
	{
		/** @var DateTimeImmutable|false $finalDate */
		$finalDate = @$date->modify($modifyFormat);
		if ($finalDate === false) {
			throw new DatetimeException('Invalid modify format passed');
		}

		return $finalDate;
	}

	public static function addHoursToDatetime(DateTimeImmutable $date, int $hours): DateTimeImmutable
	{
		return self::modifyDatetime($date, sprintf('+ %s hours', $hours));
	}

	public static function addMinutesToDatetime(DateTimeImmutable $date, int $minutes): DateTimeImmutable
	{
		return self::modifyDatetime($date, sprintf('+ %s hours', $minutes));
	}

	public static function addMonthsToDatetime(DateTimeImmutable $date, int $months): DateTimeImmutable
	{
		return self::modifyDatetime($date, sprintf('+ %s months', $months));
	}

	public static function addDaysToDatetime(DateTimeImmutable $date, int $days): DateTimeImmutable
	{
		return self::modifyDatetime($date, sprintf('+ %s days', $days));
	}

	public static function addYearsToDatetime(DateTimeImmutable $date, int $years): DateTimeImmutable
	{
		return self::modifyDatetime($date, sprintf('+ %s years', $years));
	}

	public static function addToDatetime(
		DateTimeImmutable $date,
		int $hours = 0,
		int $minutes = 0,
		int $days = 0,
		int $months = 0,
		int $years = 0
	): DateTimeImmutable {
		return self::modifyDatetime(
			$date,
			sprintf(
				'+ %s years + %s months + %s days + %s hours + %s minutes',
				$years,
				$months,
				$days,
				$hours,
				$minutes
			)
		);
	}
}

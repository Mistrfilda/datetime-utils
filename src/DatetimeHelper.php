<?php

declare(strict_types = 1);


namespace App\Mistrfilda\Datetime;


use DateTimeImmutable;


class DatetimeHelper
{
	public static function modifyDatetime(DateTimeImmutable $date, string $modifyFormat): DateTimeImmutable
	{
		$finalDate = $date->modify($modifyFormat);
		if ($finalDate === false) {
			throw new DatetimeException('Invalid modify format passed');
		}

		return $finalDate;
	}
}
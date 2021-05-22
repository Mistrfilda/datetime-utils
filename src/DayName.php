<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime;

class DayName
{

	public const MONDAY = 'Mon';

	public const TUESDAY = 'Tue';

	public const WEDNESDAY = 'Wed';

	public const THURSDAY = 'Thu';

	public const FRIDAY = 'Fri';

	public const SATURDAY = 'Sat';

	public const SUNDAY = 'Sun';

	public const WEEKEND = [
		self::SATURDAY,
		self::SUNDAY,
	];

}

<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Holiday;

use DateTimeImmutable;

class CzechHoliday
{

	private DateTimeImmutable $date;

	public function __construct(private string $name, DateTimeImmutable $date)
	{
		$this->date = $date->setTime(0, 0, 0, 0);
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getDate(): DateTimeImmutable
	{
		return $this->date;
	}

	public function getTimestamp(): int
	{
		return $this->date->getTimestamp();
	}

}

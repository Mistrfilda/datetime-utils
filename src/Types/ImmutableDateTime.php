<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Types;

use DateTimeImmutable;
use Mistrfilda\Datetime\DatetimeException;
use function assert;

class ImmutableDateTime extends DateTimeImmutable
{

	public function getYear(): int
	{
		return (int) $this->format('Y');
	}

	public function getMonth(): int
	{
		return (int) $this->format('m');
	}

	public function getDay(): int
	{
		return (int) $this->format('d');
	}

	public function getHour(): int
	{
		return (int) $this->format('H');
	}

	public function getMinutes(): int
	{
		return (int) $this->format('i');
	}

	public function getSeconds(): int
	{
		return (int) $this->format('s');
	}

	/**
	 * @return $this
	 * @throws DatetimeException
	 */
	public function modify(string $modifier): self
	{
		$modifiedDate = @parent::modify($modifier);
		assert($modifiedDate instanceof $this);
		return $modifiedDate;
	}

	public function addHoursToDatetime(int $hours): self
	{
		return $this->modify(sprintf('+ %s hours', $hours));
	}

	public function addMinutesToDatetime(int $minutes): self
	{
		return $this->modify(sprintf('+ %s minutes', $minutes));
	}

	public function addSecondsToDatetime(int $seconds): self
	{
		return $this->modify(sprintf('+ %s seconds', $seconds));
	}

	public function addMonthsToDatetime(int $months): self
	{
		return $this->modify(sprintf('+ %s months', $months));
	}

	public function addDaysToDatetime(int $days): self
	{
		return $this->modify(sprintf('+ %s days', $days));
	}

	public function addYearsToDatetime(int $years): self
	{
		return $this->modify(sprintf('+ %s years', $years));
	}

	public function deductYearsFromDatetime(int $years): self
	{
		return $this->modify(sprintf('- %s years', $years));
	}

	public function deductHoursFromDatetime(int $hours): self
	{
		return $this->modify(sprintf('- %s hours', $hours));
	}

	public function deductMinutesFromDatetime(int $minutes): self
	{
		return $this->modify(sprintf('- %s minutes', $minutes));
	}

	public function deductSecondsFromDatetime(int $seconds): self
	{
		return $this->modify(sprintf('- %s seconds', $seconds));
	}

	public function deductMonthsFromDatetime(int $months): self
	{
		return $this->modify(sprintf('- %s months', $months));
	}

	public function deductDaysFromDatetime(int $days): self
	{
		return $this->modify(sprintf('- %s days', $days));
	}

	public function addToDatetime(
		int $hours = 0,
		int $minutes = 0,
		int $days = 0,
		int $months = 0,
		int $years = 0,
	): self
	{
		return $this->modify(
			sprintf(
				'+ %s years + %s months + %s days + %s hours + %s minutes',
				$years,
				$months,
				$days,
				$hours,
				$minutes,
			),
		);
	}

	public function isWeekend(): bool
	{
		return $this->format('N') >= 6;
	}

}

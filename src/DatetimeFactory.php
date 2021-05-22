<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime;

use Mistrfilda\Datetime\Timezone\Timezone;
use Mistrfilda\Datetime\Types\DateTimeImmutable;
use Throwable;

class DatetimeFactory
{

	public const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';

	public const DEFAULT_DATE_FORMAT = 'Y-m-d';

	public const DEFAULT_MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

	public static function createFromFormat(
		string $datetime,
		string $format = self::DEFAULT_MYSQL_DATETIME_FORMAT,
	): DateTimeImmutable
	{
		$parsedDatetime = DateTimeImmutable::createFromFormat($format, $datetime);
		if ($parsedDatetime === false) {
			throw new DatetimeException('Can\'t create datetime from specified value and format');
		}

		return (new DateTimeImmutable('@' . $parsedDatetime->getTimestamp()))->setTimezone(
			$parsedDatetime->getTimezone(),
		);
	}

	public function createNow(): DateTimeImmutable
	{
		return new DateTimeImmutable();
	}

	public function createToday(): DateTimeImmutable
	{
		return (new DateTimeImmutable())->setTime(0, 0, 0);
	}

	public function createFromTimestamp(int $timestamp, string $timezone = Timezone::UTC): DateTimeImmutable
	{
		try {
			return (new DateTimeImmutable('@' . $timestamp))->setTimezone(Timezone::createTimezone($timezone));
		} catch (Throwable $e) {
			throw new DatetimeException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function createDatetimeFromMysqlFormat(
		string $datetime,
		string $mysqlDatetimeFormat = self::DEFAULT_MYSQL_DATETIME_FORMAT,
	): DateTimeImmutable
	{
		$parsedDatetime = DateTimeImmutable::createFromFormat($mysqlDatetimeFormat, $datetime);
		if ($parsedDatetime === false) {
			throw new DatetimeException('Can\'t create datetime from specified value and format');
		}

		return (new DateTimeImmutable('@' . $parsedDatetime->getTimestamp()))->setTimezone(
			$parsedDatetime->getTimezone(),
		);
	}

}

<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime;

use Mistrfilda\Datetime\Timezone\Timezone;
use Mistrfilda\Datetime\Types\ImmutableDateTime;
use Throwable;

class DatetimeFactory
{

	public const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';

	public const DEFAULT_DATE_FORMAT = 'Y-m-d';

	public const DEFAULT_MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

	public static function createFromFormat(
		string $datetime,
		string $format = self::DEFAULT_MYSQL_DATETIME_FORMAT,
	): ImmutableDateTime
	{
		$parsedDatetime = ImmutableDateTime::createFromFormat($format, $datetime);
		if ($parsedDatetime === false) {
			throw new DatetimeException('Can\'t create datetime from specified value and format');
		}

		return (new ImmutableDateTime('@' . $parsedDatetime->getTimestamp()))->setTimezone(
			$parsedDatetime->getTimezone(),
		);
	}

	public function createNow(): ImmutableDateTime
	{
		return new ImmutableDateTime();
	}

	public function createToday(): ImmutableDateTime
	{
		return (new ImmutableDateTime())->setTime(0, 0, 0);
	}

	public function createFromTimestamp(int $timestamp, string $timezone = Timezone::UTC): ImmutableDateTime
	{
		try {
			return (new ImmutableDateTime('@' . $timestamp))->setTimezone(Timezone::createTimezone($timezone));
		} catch (Throwable $e) {
			throw new DatetimeException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function createDatetimeFromMysqlFormat(
		string $datetime,
		string $mysqlDatetimeFormat = self::DEFAULT_MYSQL_DATETIME_FORMAT,
	): ImmutableDateTime
	{
		$parsedDatetime = ImmutableDateTime::createFromFormat($mysqlDatetimeFormat, $datetime);
		if ($parsedDatetime === false) {
			throw new DatetimeException('Can\'t create datetime from specified value and format');
		}

		return (new ImmutableDateTime('@' . $parsedDatetime->getTimestamp()))->setTimezone(
			$parsedDatetime->getTimezone(),
		);
	}

}

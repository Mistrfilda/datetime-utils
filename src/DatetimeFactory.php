<?php

declare(strict_types=1);

namespace Mistrfilda\Datetime;

use Mistrfilda\Datetime\Types\DatetimeImmutable;
use Throwable;

class DatetimeFactory
{
	public const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';

	public const DEFAULT_DATE_FORMAT = 'Y-m-d';

	public const DEFAULT_MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

	public function createNow(): DatetimeImmutable
	{
		return new DateTimeImmutable();
	}

	public function createToday(): DateTimeImmutable
	{
		return (new DateTimeImmutable())->setTime(0, 0, 0);
	}

	public function createFromTimestamp(int $timestamp): DateTimeImmutable
	{
		try {
			return new DateTimeImmutable('@' . $timestamp);
		} catch (Throwable $e) {
			throw new DatetimeException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function createDatetimeFromMysqlFormat(
		string $datetime,
		string $mysqlDatetimeFormat = self::DEFAULT_MYSQL_DATETIME_FORMAT
	): DateTimeImmutable {
		$parsedDatetime = DateTimeImmutable::createFromFormat($mysqlDatetimeFormat, $datetime);
		if ($parsedDatetime === false) {
			throw new DatetimeException('Can\'t create datetime from specified value and format');
		}

		return new DatetimeImmutable('@' . $parsedDatetime->getTimestamp());
	}

	public static function createFromFormat(
		string $datetime,
		string $mysqlDatetimeFormat = self::DEFAULT_MYSQL_DATETIME_FORMAT
	): DateTimeImmutable {
		$parsedDatetime = DateTimeImmutable::createFromFormat($mysqlDatetimeFormat, $datetime);
		if ($parsedDatetime === false) {
			throw new DatetimeException('Can\'t create datetime from specified value and format');
		}

		return new DatetimeImmutable('@' . $parsedDatetime->getTimestamp());
	}
}

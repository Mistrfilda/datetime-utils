<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Doctrine;

use DateTimeImmutable as BuiltInDatetimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeImmutableType as DoctrineDatetimeImmutableType;
use Doctrine\DBAL\Types\Types;
use Mistrfilda\Datetime\DatetimeException;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Datetime\Helper\ConversionExceptionHelper;
use Mistrfilda\Datetime\Types\ImmutableDateTime;

class DateImmutableType extends DoctrineDatetimeImmutableType
{

	/**
	 * @throws ConversionException
	 */
	public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string|null
	{
		if ($value === null) {
			return $value;
		}

		if ($value instanceof BuiltInDatetimeImmutable) {
			return $value->format($platform->getDateFormatString());
		}

		throw ConversionExceptionHelper::conversionFailedInvalidType(
			$value,
			Types::DATETIME_IMMUTABLE,
			['null', ImmutableDateTime::class],
		);
	}

	/**
	 * @throws ConversionException
	 */
	public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ImmutableDateTime|null
	{
		if ($value === null || $value instanceof ImmutableDateTime) {
			return $value;
		}

		if (is_string($value) === false) {
			throw ConversionExceptionHelper::conversionFailedFormat(
				$value,
				Types::DATETIME_IMMUTABLE,
				$platform->getDateFormatString(),
			);
		}

		try {
			return DatetimeFactory::createFromFormat($value, $platform->getDateTimeFormatString());
		} catch (DatetimeException) {
			throw ConversionExceptionHelper::conversionFailedFormat(
				$value,
				Types::DATETIME_IMMUTABLE,
				$platform->getDateFormatString(),
			);
		}
	}

}

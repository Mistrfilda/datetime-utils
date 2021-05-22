<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Doctrine;

use DatetimeImmutable as BuiltInDatetimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeImmutableType as DoctrineDatetimeImmutableType;
use Mistrfilda\Datetime\DatetimeException;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Datetime\Types\DateTimeImmutable;

class DatetimeImmutableType extends DoctrineDatetimeImmutableType
{

	/**
	 * @throws ConversionException
	 */
	public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
	{
		if ($value === null) {
			return $value;
		}

		if ($value instanceof BuiltInDatetimeImmutable) {
			return $value->format($platform->getDateTimeFormatString());
		}

		throw ConversionException::conversionFailedInvalidType(
			$value,
			$this->getName(),
			['null', DateTimeImmutable::class],
		);
	}

	/**
	 * @throws ConversionException
	 */
	public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
	{
		if ($value === null || $value instanceof DateTimeImmutable) {
			return $value;
		}

		try {
			return DatetimeFactory::createFromFormat($value, $platform->getDateTimeFormatString());
		} catch (DatetimeException) {
			throw ConversionException::conversionFailedFormat(
				$value,
				$this->getName(),
				$platform->getDateTimeFormatString(),
			);
		}
	}

}

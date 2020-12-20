<?php

declare(strict_types=1);

namespace Mistrfilda\Datetime\Doctrine;

use DateTimeImmutable as BuiltInDatetimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeImmutableType as DoctrineDatetimeImmutableType;
use Mistrfilda\Datetime\DatetimeException;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

class DateImmutableType extends DoctrineDatetimeImmutableType
{
	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if ($value === null) {
			return $value;
		}

		if ($value instanceof BuiltInDatetimeImmutable) {
			return $value->format($platform->getDateFormatString());
		}

		throw ConversionException::conversionFailedInvalidType(
			$value,
			$this->getName(),
			['null', DateTimeImmutable::class]
		);
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if ($value === null || $value instanceof DateTimeImmutable) {
			return $value;
		}

		try {
			return DatetimeFactory::createFromFormat($value, $platform->getDateFormatString());
		} catch (DatetimeException $e) {
			throw ConversionException::conversionFailedFormat(
				$value,
				$this->getName(),
				$platform->getDateFormatString()
			);
		}
	}
}

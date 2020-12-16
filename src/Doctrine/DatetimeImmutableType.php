<?php

declare(strict_types=1);

namespace Mistrfilda\Datetime\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeImmutableType as DoctrineDatetimeImmutableType;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

class DatetimeImmutableType extends DoctrineDatetimeImmutableType
{
	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if ($value === null) {
			return $value;
		}

		if ($value instanceof DatetimeImmutable) {
			return $value->format($platform->getDateTimeFormatString());
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

		return DatetimeFactory::createFromFormat( $value, $platform->getDateTimeFormatString());
	}
}

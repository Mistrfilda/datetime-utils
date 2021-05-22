<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Doctrine;

use DateTime;
use DateTimeImmutable as BuiltInDatetimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeImmutableType as DoctrineDatetimeImmutableType;
use Mistrfilda\Datetime\DatetimeException;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Datetime\Types\DateTimeImmutable;

class DateImmutableType extends DoctrineDatetimeImmutableType
{

	/**
	 * @param mixed $value
	 * @return mixed|string|null
	 * @throws ConversionException
	 */
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
			['null', DateTimeImmutable::class],
		);
	}

	/**
	 * @param mixed $value
	 * @return DateTime|BuiltInDatetimeImmutable|DateTimeInterface|DateTimeImmutable|mixed|null
	 * @throws ConversionException
	 */
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
				$platform->getDateFormatString(),
			);
		}
	}

}

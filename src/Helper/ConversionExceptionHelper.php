<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Helper;

use Doctrine\DBAL\Types\ConversionException;
use Throwable;

class ConversionExceptionHelper
{

	public static function conversionFailedFormat(
		mixed $value,
		string $toType,
		string $expectedFormat,
		Throwable|null $previous = null,
	): ConversionException
	{
		$value = is_scalar($value) ? (string) $value : gettype($value);
		$value = strlen($value) > 32 ? substr($value, 0, 20) . '...' : $value;

		return new ConversionException(
			'Could not convert database value "' . $value . '" to Doctrine Type ' .
			$toType . '. Expected format: ' . $expectedFormat,
			0,
			$previous,
		);
	}

	/**
	 * @param array<string> $possibleTypes
	 */
	public static function conversionFailedInvalidType(
		mixed $value,
		string $toType,
		array $possibleTypes,
		Throwable|null $previous = null,
	): ConversionException
	{
		if (is_scalar($value) || $value === null) {
			return new ConversionException(sprintf(
				'Could not convert PHP value %s to type %s. Expected one of the following types: %s',
				var_export($value, true),
				$toType,
				implode(', ', $possibleTypes),
			), 0, $previous);
		}

		return new ConversionException(sprintf(
			'Could not convert PHP value of type %s to type %s. Expected one of the following types: %s',
			is_object($value) ? $value::class : gettype($value),
			$toType,
			implode(', ', $possibleTypes),
		), 0, $previous);
	}

}

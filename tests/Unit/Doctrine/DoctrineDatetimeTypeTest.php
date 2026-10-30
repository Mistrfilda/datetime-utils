<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Tests\Unit\Doctrine;

use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Mistrfilda\Datetime\Doctrine\DateImmutableType;
use Mistrfilda\Datetime\Doctrine\DatetimeImmutableType;
use Mistrfilda\Datetime\Tests\Unit\BaseUnitTest;
use Mistrfilda\Datetime\Timezone\Timezone;
use Mistrfilda\Datetime\Types\ImmutableDateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

class DoctrineDatetimeTypeTest extends BaseUnitTest
{

	// phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong
	protected AbstractPlatform $platform;

	public function testDateTypeRoundTrip(): void
	{
		$type = new DateImmutableType();
		$date = $type->convertToPHPValue('2026-10-30', $this->platform);
		self::assertNotNull($date);
		self::assertSame('2026-10-30 00:00:00.000000', $date->format('Y-m-d H:i:s.u'));
		self::assertSame('2026-10-30', $type->convertToDatabaseValue($date, $this->platform));
		self::assertSame('DATE', $type->getSQLDeclaration([], $this->platform));
	}

	public function testDateTimeImmutableType(): void
	{
		$type = new DatetimeImmutableType();
		$date = new ImmutableDateTime('2024-02-29 23:45:12');

		$dbValue = $type->convertToDatabaseValue($date, $this->platform);
		self::assertSame('2024-02-29 23:45:12', $dbValue);
		self::assertSame('DATETIME', $type->getSQLDeclaration([], $this->platform));

		//@phpstan-ignore-next-line
		self::assertNull(
			$type->convertToDatabaseValue(null, $this->platform),
		);

		self::assertException(
			function () use ($type): void {
				$type->convertToDatabaseValue('12', $this->platform);
			},
			ConversionException::class,
			'Could not convert PHP value \'12\' to type datetime_immutable. Expected one of the following types: null, Mistrfilda\Datetime\Types\ImmutableDateTime',
		);

		$datetimeValue = $type->convertToPHPValue(
			'2024-02-29 23:45:12',
			$this->platform,
		);

		self::assertTrue($datetimeValue instanceof DateTimeImmutable);
		self::assertDatetimeImmutable($date, $datetimeValue);
		self::assertSame('000000', $datetimeValue->format('u'));
		self::assertNull($type->convertToPHPValue(null, $this->platform));

		self::assertException(
			function () use ($type): void {
				$type->convertToPHPValue('12', $this->platform);
			},
			ConversionException::class,
			'Could not convert database value "12" to Doctrine Type datetime_immutable. Expected format: Y-m-d H:i:s',
		);
	}

	public function testDateImmutableType(): void
	{
		$type = new DateImmutableType();
		$date = new ImmutableDateTime('2024-02-29 23:45:12.123456');

		$dbValue = $type->convertToDatabaseValue($date, $this->platform);
		self::assertSame('2024-02-29', $dbValue);

		//@phpstan-ignore-next-line
		self::assertNull(
			$type->convertToDatabaseValue(null, $this->platform),
		);

		self::assertException(
			function () use ($type): void {
				$type->convertToDatabaseValue('12', $this->platform);
			},
			ConversionException::class,
			'Could not convert PHP value \'12\' to type date_immutable. Expected one of the following types: null, Mistrfilda\Datetime\Types\ImmutableDateTime',
		);

		$datetimeValue = $type->convertToPHPValue(
			'2024-02-29',
			$this->platform,
		);

		self::assertTrue($datetimeValue instanceof DateTimeImmutable);
		self::assertDatetimeImmutable(new ImmutableDateTime('2024-02-29 00:00:00'), $datetimeValue);
		self::assertSame('000000', $datetimeValue->format('u'));
		self::assertNull($type->convertToPHPValue(null, $this->platform));

		self::assertException(
			function () use ($type): void {
				$type->convertToPHPValue('12', $this->platform);
			},
			ConversionException::class,
			'Could not convert database value "12" to Doctrine Type date_immutable. Expected format: Y-m-d',
		);
	}

	/**
	 * @param class-string<DateImmutableType|DatetimeImmutableType> $typeClass
	 * @throws ConversionException
	 */
	#[DataProvider('provideDoctrineTypes')]
	public function testExistingImmutableDateIsReturnedUnchanged(string $typeClass): void
	{
		$type = new $typeClass();
		$date = new ImmutableDateTime('2024-02-29 23:45:12.123456', Timezone::getPragueTimezone());
		self::assertSame($date, $type->convertToPHPValue($date, $this->platform));
	}

	/** @return array<string, array{class-string<DateImmutableType|DatetimeImmutableType>}> */
	public static function provideDoctrineTypes(): array
	{
		return [
			'date' => [DateImmutableType::class],
			'datetime' => [DatetimeImmutableType::class],
		];
	}

	public function testBuiltinImmutableDatesCanBeStored(): void
	{
		$date = new DateTimeImmutable('2024-02-29 23:45:12.123456', Timezone::getPragueTimezone());
		self::assertSame('2024-02-29', new DateImmutableType()->convertToDatabaseValue($date, $this->platform));
		self::assertSame(
			'2024-02-29 23:45:12',
			new DatetimeImmutableType()->convertToDatabaseValue($date, $this->platform),
		);
		self::assertSame('2024-02-29 23:45:12.123456 Europe/Prague', $date->format('Y-m-d H:i:s.u e'));
	}

	#[DataProvider('provideNonStringDatabaseValues')]
	public function testDateRejectsNonStringDatabaseValues(mixed $value, string $description): void
	{
		$this->expectException(ConversionException::class);
		$this->expectExceptionMessageIs(
			'Could not convert database value "' . $description . '" to Doctrine Type date_immutable. Expected format: Y-m-d',
		);
		new DateImmutableType()->convertToPHPValue($value, $this->platform);
	}

	#[DataProvider('provideNonStringDatabaseValues')]
	public function testDatetimeRejectsNonStringDatabaseValues(mixed $value, string $description): void
	{
		$this->expectException(ConversionException::class);
		$this->expectExceptionMessageIs(
			'Could not convert database value "' . $description . '" to Doctrine Type datetime_immutable. Expected format: Y-m-d H:i:s',
		);
		new DatetimeImmutableType()->convertToPHPValue($value, $this->platform);
	}

	/** @return array<string, array{mixed, string}> */
	public static function provideNonStringDatabaseValues(): array
	{
		return [
			'integer' => [42, '42'],
			'boolean' => [false, ''],
			'array' => [[], 'array'],
			'object' => [new stdClass(), 'object'],
		];
	}

	#[DataProvider('provideInvalidPhpValues')]
	public function testDateRejectsInvalidPhpValues(mixed $value, string $description): void
	{
		$this->expectException(ConversionException::class);
		$this->expectExceptionMessageIs(
			'Could not convert PHP value ' . $description . ' to type date_immutable. '
			. 'Expected one of the following types: null, Mistrfilda\Datetime\Types\ImmutableDateTime',
		);
		new DateImmutableType()->convertToDatabaseValue($value, $this->platform);
	}

	#[DataProvider('provideInvalidPhpValues')]
	public function testDatetimeRejectsInvalidPhpValues(mixed $value, string $description): void
	{
		$this->expectException(ConversionException::class);
		$this->expectExceptionMessageIs(
			'Could not convert PHP value ' . $description . ' to type datetime_immutable. '
			. 'Expected one of the following types: null, Mistrfilda\Datetime\Types\ImmutableDateTime',
		);
		new DatetimeImmutableType()->convertToDatabaseValue($value, $this->platform);
	}

	/** @return array<string, array{mixed, string}> */
	public static function provideInvalidPhpValues(): array
	{
		return [
			'integer' => [42, '42'],
			'boolean' => [false, 'false'],
			'array' => [[], 'of type array'],
			'object' => [new stdClass(), 'of type stdClass'],
			'mutable date' => [new DateTime('2024-02-29 12:34:56'), 'of type DateTime'],
		];
	}

	#[DataProvider('provideInvalidDateStrings')]
	public function testDateRejectsInvalidCalendarDates(string $value): void
	{
		$this->expectException(ConversionException::class);
		$this->expectExceptionMessageIsOrContains('to Doctrine Type date_immutable. Expected format: Y-m-d');
		new DateImmutableType()->convertToPHPValue($value, $this->platform);
	}

	/** @return array<string, array{string}> */
	public static function provideInvalidDateStrings(): array
	{
		return [
			'non-leap February' => ['2023-02-29'],
			'April overflow' => ['2024-04-31'],
			'time in date-only value' => ['2024-02-29 12:34:56'],
			'null byte' => ["2024-02-29\0"],
		];
	}

	#[DataProvider('provideInvalidDatetimeStrings')]
	public function testDatetimeRejectsInvalidCalendarDates(string $value): void
	{
		$this->expectException(ConversionException::class);
		$this->expectExceptionMessageIsOrContains('to Doctrine Type datetime_immutable. Expected format: Y-m-d H:i:s');
		new DatetimeImmutableType()->convertToPHPValue($value, $this->platform);
	}

	/** @return array<string, array{string}> */
	public static function provideInvalidDatetimeStrings(): array
	{
		return [
			'non-leap February' => ['2023-02-29 12:34:56'],
			'April overflow' => ['2024-04-31 12:34:56'],
			'invalid hour' => ['2024-02-29 24:00:00'],
			'missing time' => ['2024-02-29'],
			'null byte' => ["2024-02-29 12:34:56\0"],
		];
	}

	public function testLongInvalidValuesAreAbbreviatedInErrors(): void
	{
		$this->expectException(ConversionException::class);
		$this->expectExceptionMessageIs(
			'Could not convert database value "2024-02-29 12:34:56 ..." to Doctrine Type datetime_immutable. Expected format: Y-m-d H:i:s',
		);
		new DatetimeImmutableType()->convertToPHPValue('2024-02-29 12:34:56 trailing invalid data', $this->platform);
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->platform = new MySQLPlatform();
	}
	// phpcs:enable SlevomatCodingStandard.Files.LineLength.LineTooLong

}

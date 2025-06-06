<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Tests\Unit\Doctrine;

use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Mistrfilda\Datetime\Doctrine\DateImmutableType;
use Mistrfilda\Datetime\Doctrine\DatetimeImmutableType;
use Mistrfilda\Datetime\Tests\Unit\BaseUnitTest;

class DoctrineDatetimeTypeTest extends BaseUnitTest
{

	// phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong
	protected AbstractPlatform $platform;

	public function testDateTimeImmutableType(): void
	{
		$type = new DatetimeImmutableType();

		$dbValue = $type->convertToDatabaseValue($this->now, $this->platform);
		self::assertSame(
			$this->now->format($this->platform->getDateTimeFormatString()),
			$dbValue,
		);

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
			$this->now->format($this->platform->getDateTimeFormatString()),
			$this->platform,
		);

		self::assertTrue($datetimeValue instanceof DateTimeImmutable);
		self::assertDatetimeImmutable($this->now, $datetimeValue);
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

		$dbValue = $type->convertToDatabaseValue($this->now, $this->platform);
		self::assertSame(
			$this->now->format($this->platform->getDateFormatString()),
			$dbValue,
		);

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
			$this->now->format($this->platform->getDateTimeFormatString()),
			$this->platform,
		);

		self::assertTrue($datetimeValue instanceof DateTimeImmutable);
		self::assertDatetimeImmutable($this->now, $datetimeValue);
		self::assertNull($type->convertToPHPValue(null, $this->platform));

		self::assertException(
			function () use ($type): void {
				$type->convertToPHPValue('12', $this->platform);
			},
			ConversionException::class,
			'Could not convert database value "12" to Doctrine Type datetime_immutable. Expected format: Y-m-d',
		);
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->platform = new MySQLPlatform();
	}
	// phpcs:enable SlevomatCodingStandard.Files.LineLength.LineTooLong

}

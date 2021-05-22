<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Tests\Unit;

use DateTimeImmutable as BuiltInDatetimeImmutable;
use Mistrfilda\Datetime\DatetimeException;
use Mistrfilda\Datetime\DatetimeFactory;
use Mockery;

class DatetimeFactoryTest extends BaseUnitTest
{

	private DatetimeFactory $datetimeFactory;

	public function testCreateNow(): void
	{
		self::assertDatetimeImmutable(
			$this->now,
			$this->datetimeFactory->createNow(),
		);
	}

	public function testCreateToday(): void
	{
		self::assertDatetimeImmutable(
			$this->now->setTime(0, 0, 0),
			$this->datetimeFactory->createToday(),
		);
	}

	public function testCreateFromTimestamp(): void
	{
		self::assertDatetimeImmutable(
			$this->now,
			$this->datetimeFactory->createFromTimestamp($this->now->getTimestamp()),
		);
	}

	public function testCreateFromMysqlFormat(): void
	{
		$testDatetime = (new BuiltInDatetimeImmutable())->setTime(23, 0, 0);

		$createdDatetime = $this->datetimeFactory->createDatetimeFromMysqlFormat(
			$testDatetime->format(DatetimeFactory::DEFAULT_MYSQL_DATETIME_FORMAT),
		);

		self::assertDatetimeImmutable($testDatetime, $createdDatetime);

		self::assertException(function () use ($testDatetime): void {
			$this->datetimeFactory->createDatetimeFromMysqlFormat(
				$testDatetime->format(DatetimeFactory::DEFAULT_MYSQL_DATETIME_FORMAT),
				'Yysf',
			);
		}, DatetimeException::class, 'Can\'t create datetime from specified value and format');
	}

	protected function setUp(): void
	{
		parent::setUp();
		$datetimeFactoryMock = Mockery::mock(DatetimeFactory::class)->makePartial();
		$datetimeFactoryMock->shouldReceive('createNow')->andReturn($this->now);
		$datetimeFactoryMock->shouldReceive('createToday')->andReturn($this->now->setTime(0, 0, 0));

		$this->datetimeFactory = $datetimeFactoryMock;
	}

}

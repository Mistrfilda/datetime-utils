<?php

declare(strict_types = 1);

namespace Mistrfilda\Datetime\Tests;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Throwable;
use function get_class;
use function sprintf;

abstract class UpdatedTestCase extends TestCase
{

	/**
	 * Asserts that the given callback throws the given exception.
	 *
	 * @param callable $callback A callback which should throw the exception
	 * @param class-string $expectClass The name of the expected exception class
	 * @param string|null $message Exception message
	 */
	protected static function assertException(
		callable $callback,
		string $expectClass,
		?string $message = null
	): void
	{
		try {
			$callback();
		} catch (Throwable $exception) {
			self::assertInstanceOf($expectClass, $exception, 'An invalid exception was thrown');

			if ($message !== null) {
				self::assertSame($message, $exception->getMessage());
			}

			return;
		}

		self::fail('No exception was thrown');
	}

	protected static function assertNoError(callable $callback): void
	{
		try {
			$callback();
		} catch (Throwable $exception) {
			self::fail(
				sprintf(
					'Exception %s - %s occured while calling noError assertion',
					get_class($exception),
					$exception->getMessage(),
				),
			);
		}

		/**
		 * Ignore, we know that this value is always true, but there must be some assertion
		 *
		 * @phpstan-ignore-next-line
		 */
		self::assertTrue(true);
	}

	protected static function assertDatetimeImmutable(
		DateTimeImmutable $expected,
		DateTimeImmutable $actual
	): void
	{
		self::assertSame($expected->getTimestamp(), $actual->getTimestamp());
		self::assertSame($expected->getTimezone()->getName(), $actual->getTimezone()->getName());
	}

}

# datetime-utils

A small PHP library for working with immutable dates and times, built on PHP's
native `DateTimeImmutable`. It provides a factory for creating and parsing dates,
convenient date arithmetic, access to individual date components and timezone helpers.

## Installation

Requires PHP 8.4 or newer and the `calendar` extension (`ext-calendar`).
Tested on PHP 8.4 and 8.5.

```bash
composer require mistrfilda/datetime
```

## Basic usage

```php
use Mistrfilda\Datetime\DatetimeFactory;

require __DIR__ . '/vendor/autoload.php';

$factory = new DatetimeFactory();
$now = $factory->createNow();
$today = $factory->createToday(); // Today at midnight

$date = DatetimeFactory::createFromFormat('2026-10-30', '!Y-m-d');
$datetime = $factory->createDatetimeFromMysqlFormat('2026-10-30 12:34:56');
$fromTimestamp = $factory->createFromTimestamp(0); // 1970-01-01 00:00:00 UTC

echo $datetime->format('d.m.Y H:i'); // 30.10.2026 12:34
```

Factory methods return `Mistrfilda\Datetime\Types\ImmutableDateTime`, so standard
`DateTimeImmutable` methods such as `format()`, `modify()` and `setTimezone()` are
also available. Dates use PHP's default timezone unless one is supplied;
`createFromTimestamp()` defaults to UTC.

Parsing preserves microseconds and any timezone specified in the input. Invalid
input, including impossible calendar dates, raises `Mistrfilda\Datetime\DatetimeException`.
The `!` in `!Y-m-d` resets unspecified time fields to midnight.

## Working with dates

```php
$date = DatetimeFactory::createFromFormat('2026-10-30 12:00:00');

$later = $date->addDaysToDatetime(3);
$earlier = $date->deductHoursFromDatetime(2);
$shifted = $date->addToDatetime(hours: 2, days: 1);

echo $later->format('Y-m-d H:i:s');   // 2026-11-02 12:00:00
echo $earlier->format('Y-m-d H:i:s'); // 2026-10-30 10:00:00
echo $shifted->format('Y-m-d H:i:s'); // 2026-10-31 14:00:00
echo $date->format('Y-m-d H:i:s');    // 2026-10-30 12:00:00 (unchanged)

$date->getYear();    // 2026
$date->getMonth();   // 10
$date->getDay();     // 30
$date->getHour();    // 12
$date->getMinutes(); // 0
$date->getSeconds(); // 0
$date->isWeekend();  // false
```

Arithmetic methods return a new instance and preserve microseconds and timezone.
Offsets may be positive, zero or negative; a negative deduction adds the amount.
They follow PHP's calendar arithmetic, including month overflow (January 31, 2024
plus one month becomes March 2) and days lasting 23 or 25 hours across clock changes.

## Timezones

```php
use Mistrfilda\Datetime\Timezone\Timezone;

$prague = $factory->createFromTimestamp(0, Timezone::PRAGUE);
$utc = $prague->setTimezone(Timezone::getUTCTimezone());

echo $prague->format('Y-m-d H:i:s P'); // 1970-01-01 01:00:00 +01:00
echo $utc->format('Y-m-d H:i:s P');    // 1970-01-01 00:00:00 +00:00
```

`Timezone::createTimezone()` also accepts any timezone identifier supported by PHP.

## Doctrine DBAL

The optional `Mistrfilda\Datetime\Doctrine\DateImmutableType` and
`DatetimeImmutableType` integrate the custom datetime class with Doctrine DBAL 4.
The date type stores SQL `DATE` and reads values at midnight; the datetime type
stores SQL `DATETIME`. Install DBAL when using these types:

```bash
composer require "doctrine/dbal:^4.0"
```

## Czech holidays

`Mistrfilda\Datetime\Holiday\CzechHolidayService` provides Czech public holiday
lookups and yearly lists, including Good Friday and Easter Monday. It supports
years 2020–32767 using the same holiday rules throughout that range.

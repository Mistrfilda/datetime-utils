# datetime-utils

Date and time helpers, Czech public holidays and optional Doctrine DBAL types.
Requires PHP 8.4 or newer and the `calendar` extension. CI runs on PHP 8.4 and 8.5.

```bash
composer require mistrfilda/datetime
```

## Czech holidays

```php
use Mistrfilda\Datetime\Holiday\CzechHolidayService;

$holidays = new CzechHolidayService(new DateTimeZone('Europe/Prague'));

$holidays->isDateHoliday(24, 12, 2031); // true
$holidays->isDateTimeHoliday(new DateTimeImmutable('2031-04-14 15:00:00')); // true
$holidays->getCzechHolidayByDayMonthYear(14, 4, 2031)?->getName(); // Velikonoční pondělí

foreach ($holidays->getYearHolidays(2031) as $timestamp => $holiday) {
    echo $holiday->getDate()->format('Y-m-d') . ': ' . $holiday->getName() . PHP_EOL;
}
```

The 11 fixed holidays are defined once. Good Friday and Easter Monday are calculated
using PHP's [`easter_days()`](https://www.php.net/manual/en/function.easter-days.php)
and calendar-day arithmetic, including transitions to daylight saving time.
There is no table of years to maintain.

- `getYearHolidays()` returns 13 holidays in chronological order, keyed by their
  midnight timestamps. Results are cached per service instance.
- `getCurrentYearHolidays()` determines the current year on every call.
- The optional constructor timezone controls returned dates and timestamp keys.
  When omitted, the service captures PHP's default timezone at construction.
- DateTime lookups use the calendar date in the **input object's timezone** and
  ignore its time of day. Convert the input to `Europe/Prague` first if you want
  to check the Czech date of an instant.
- Supported years are 2020–32767, within PHP's `checkdate()` range. Earlier years
  remain unsupported, as in the original library. Historical holiday rules are
  not modelled; future years use the same holiday rules as the existing calendar.
- Unsupported years return `[]`; lookups of unsupported years or invalid dates
  return `false` or `null`. Invalid dates are not rolled into the following month.

The existing method names, holiday names and timestamp keys are preserved.

## Date and time helpers

```php
use Mistrfilda\Datetime\DatetimeFactory;

$factory = new DatetimeFactory();
$now = $factory->createNow();
$today = $factory->createToday();
$date = DatetimeFactory::createFromFormat('2026-10-30', '!Y-m-d');
$later = $date->addDaysToDatetime(3);
```

Factory methods return `Mistrfilda\Datetime\Types\ImmutableDateTime`, which extends
PHP's `DateTimeImmutable`. Parsing preserves microseconds and the parsed timezone.
Invalid input, including impossible calendar dates, raises `DatetimeException`.
Use `!` in a format to reset fields that the input does not specify, as with PHP's
native `createFromFormat()`.

## Doctrine

The optional `Mistrfilda\Datetime\Doctrine\DateImmutableType` and
`DatetimeImmutableType` integrate the custom datetime class with Doctrine DBAL 4.
Install `doctrine/dbal:^4.0` when using them. The date type stores SQL `DATE` and
reads `Y-m-d` values at midnight; the datetime type stores SQL `DATETIME`.

When upgrading, review generated schema changes: `DateImmutableType` previously
inherited the SQL `DATETIME` declaration and incorrectly expected a time when
reading a date. It now uses Doctrine's date type and date format.
Parsing also now rejects invalid dates that were previously silently normalized.

## Development

```bash
composer install
mkdir -p tmp
composer build-all
```

This runs syntax checks, PHPStan at the maximum level, coding standards and PHPUnit.
Composer scripts use the PHP interpreter running Composer. Static analysis and
coding standards target PHP 8.4 so changes remain compatible with both tested PHP
versions.

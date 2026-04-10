<?php

namespace Hargreaves\HolidayProvider;

class HolidayProvider
{
    public function __construct(
        private readonly array $holidayData
    ) {}

    /**
     * Get all holidays for a given state and year.
     *
     * @param string $state region key (e.g. 'kuala-lumpur', 'hong-kong')
     * @param int|null $year Year (defaults to current year)
     * @return array List of holidays in format:
     *               [
     *                   ['date' => 'YYYY-MM-DD', 'name' => 'Holiday Name']
     *               ]
     */
    public function getHolidays(string $state, ?int $year = null): array
    {
        $year ??= (int) date('Y');

        return $this->holidayData[$year][$state] ?? [];
    }

    /**
     * Check if a given date is a holiday in the specified state.
     *
     * @param string $state region key
     * @param string|\DateTimeInterface $date Date to check (Y-m-d string or DateTimeInterface)
     * @return bool True if holiday, false otherwise
     */
    public function isHoliday(string $state, string|\DateTimeInterface $date): bool
    {
        $dateString = $date instanceof \DateTimeInterface
            ? $date->format('Y-m-d')
            : $date;

        $year = (int) substr($dateString, 0, 4);

        foreach ($this->getHolidays($state, $year) as $holiday) {
            if ($holiday['date'] === $dateString) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the next holiday(s) after a given date.
     *
     * Returns all holidays that fall on the next available holiday date.
     * If no holiday exists in the current year, it checks the next year.
     *
     * @param string $state region key
     * @param \DateTimeInterface $fromDate Starting date
     * @return array|null List of holidays on the next date, or null if none found
     */
    public function getNextHoliday(string $state, \DateTimeInterface $fromDate): ?array
    {
        $currentYear = (int) $fromDate->format('Y');
        $fromDateString = $fromDate->format('Y-m-d');

        $holidays = $this->getHolidays($state, $currentYear);

        $nextDate = null;
        $result = [];

        foreach ($holidays as $holiday) {
            if ($holiday['date'] >= $fromDateString) {

                if ($nextDate === null) {
                    $nextDate = $holiday['date'];
                }

                if ($holiday['date'] === $nextDate) {
                    $result[] = $holiday;
                } else {
                    break;
                }
            }
        }

        if (!empty($result)) {
            return $result;
        }

        $nextYearHolidays = $this->getHolidays($state, $currentYear + 1);

        if (empty($nextYearHolidays)) {
            return null;
        }

        $nextDate = $nextYearHolidays[0]['date'];

        return array_values(array_filter(
            $nextYearHolidays,
            fn ($h) => $h['date'] === $nextDate
        ));
    }
}

<?php

$datesByYear = [];

$currentYear = (int) date('Y');
$startYear = $currentYear - 3;
$endYear = $currentYear + 3;

for ($year = $startYear; $year <= $endYear; $year++) {
    $dates = [];

    $start = new DateTimeImmutable("$year-01-01");
    $end = new DateTimeImmutable("$year-12-31");

    $period = new DatePeriod(
        $start,
        new DateInterval('P1D'),
        $end->modify('+1 day')
    );

    foreach ($period as $date) {
        if ($date->format('w') === '0') {
            $dates[] = [
                'date' => $date->format('Y-m-d'),
                'name' => 'Sunday',
            ];
        }
    }

    $datesByYear[$year] = [
        'sunday' => $dates,
    ];
}

return $datesByYear;

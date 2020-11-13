<?php

require_once 'config/settings.php';
try {
    exit(main($argc, $argv));
} catch (Throwable $e) {
    echo "Exception: <" . get_class($e) . "> : '" . $e->getMessage() . "'\n";
    echo "Trace:\n{$e->getTraceAsString()}\n";
    exit(255);
}

function main(int $argc, array $argv) : int
{
    $rs = reports_get_latest_for_all();
    $sum = reports_compile_summary_data($rs);
    $vec = reports_format_summary_data_vector($sum);

    foreach ($vec as $row)
    {
        fputcsv(STDOUT, $row);
    }

    return 0;
}

function reports_format_summary_data_vector(array $sum) : array
{
    $out = [];

    // course_id
    // errors
    // suggestions
    // date_run
    // issues[type=summary].error
    // issues[type=summary].suggestion

    $out[] = array(
        'COURSE_ID',
        'COURSE_NAME',
        'DATE_RUN',
        'ERRORS_TOTAL',
        'SUGGESTIONS_TOTAL',
        'CLASS',
        'TYPE',
        'N',
    );

    foreach ($sum as $row)
    {
        $common = array(
            $row->course_id,
            $row->course_name,
            $row->date_run->format('Y-m-d H:i'),
            $row->errors,
            $row->suggestions,
        );

        // pick off the summary item from the issues array (should be only one)
        $summary = reset(array_filter($row->issues, function (object $x) {
            return $x->type == 'summary';
        }));

        foreach (['error', 'suggestion', 'warning'] as $klass)
        {
            foreach ($summary->{$klass} as $kind => $n)
            {
                $special = [$klass, $kind, $n];
                $out[] = array_merge($common, $special);
            }
        }
    }

    return $out;
}

function reports_compile_summary_data(array $rs) : array
{
    $out = [];

    foreach ($rs as $r)
    {
        $rd = reports_get_results($r->id);

        $course = clone $r;
        $course->issues = [];

        $ea = [];
        $wa = [];
        $sa = [];

        // loop over each group
        foreach ($rd as $kind => $d)
        {
            if ($d === null) {
                continue;
            }

            // title, items, amount
            // items have error, warning, suggestion

            $ea[] = $e = (column(flatten(column($d->items, 'error')), 'type'));
            $wa[] = $w = (column(flatten(column($d->items, 'warning')), 'type'));
            $sa[] = $s = (column(flatten(column($d->items, 'suggestion')), 'type'));

            $course->issues[] = (object) array(
                'type' => $kind,
                'error' => count_types($e),
                'warning' => count_types($w),
                'suggestion' => count_types($s),
            );
        }

        $course->issues[] = (object) array(
            'type' => 'summary',
            'error' => count_types(flatten($ea)),
            'warning' => count_types(flatten($wa)),
            'suggestion' => count_types(flatten($sa)),
        );

        $out[] = $course;
    }

    return $out;
}

function reports_get_latest_for_all(int $days = 10) : array
{
    if ($days < 0) {
        throw new \InvalidArgumentException('days');
    }
    $safe_days = $days;

    $sql = "SELECT r.id, r.course_id, r.date_run, r.errors, r.suggestions FROM reports r"
        . " JOIN ("
        . "     SELECT reports.course_id, MAX(reports.id) report_id"
        . "     FROM reports"
        . "     WHERE reports.date_run >= DATE(NOW() - INTERVAL " . $safe_days . " DAY)"
        . "     GROUP BY reports.course_id"
        . " ) t"
        . " ON t.report_id=r.id";

    $stmt = UdoitDB::prepare($sql);
    db_exec($stmt);

    $rs = [];

    while ($row = $stmt->fetch(PDO::FETCH_OBJ))
    {
        $date_run = strtotime($row->date_run);
        if ($date_run > 0) {
            $date_run_o = new \DateTime();
            $date_run_o->setTimestamp($date_run);
        } else {
            $date_run_o = null;
        }
        $rs[] = (object) [
            'id' => intval($row->id),
            'course_id' => intval($row->course_id),
            'errors' => intval($row->errors),
            'suggestions' => intval($row->suggestions),
            'date_run' => $date_run_o,
        ];
    }

    return $rs;
}

function reports_get_results(int $report_id)
{
    global $db_reports_table;

    $sql = "SELECT * FROM {$db_reports_table} WHERE id = :key";
    $stmt = UdoitDB::prepare($sql);
    $stmt->bindValue(':key', $report_id, PDO::PARAM_INT);

    db_exec($stmt);

    $report_json = $stmt->fetch(PDO::FETCH_OBJ)->report_json;
    $report = json_decode($report_json);
    if ($report === null) {
        $json_error = json_last_error_msg();
        throw new RuntimeException("json_decode: '$json_error'");
    }

    $ordered_report_groups = UdoitUtils::instance()->sortReportGroups($report->content);
    var_dump($ordered_report_groups); exit;
    return $ordered_report_groups;

    //echo json_encode($report, JSON_PRETTY_PRINT) . "\n";
}

function db_exec(PDOStatement $stmt) : void
{
    if (! $stmt->execute()) {
        var_dump($sql, $stmt->errorInfo());
        throw new RuntimeException("query failed");
    }
}

function flatten(array $a) : array
{
    return array_reduce($a, function (array $c, array $i) {
        return array_merge($c, $i);
    }, []);
}

function column(array $a, string $c) : array
{
    return array_column($a, $c);
}

function count_types(array $a) : array
{
    return array_reduce($a, function (array $c, string $t) {
        if (! isset($c[$t])) {
            $c[$t] = 0;
        }
        $c[$t] += 1;
        return $c;
    }, []);
}

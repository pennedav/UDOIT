<?php

require_once 'config/settings.php';

// my user id from the udoit.users table not Canvas user id
define('MY_USER_ID', 4414);

define('MY_BASE_URL', 'https://tabor.test.instructure.com');

try {
    exit(main($argc, $argv));
} catch (Throwable $e) {
    echo "Error: <" . get_class($e) . ">: " . $e->getMessage() . "\n";
    exit(255);
}

function main(int $argc, array $argv) : int
{
    $search = $argv[1];

    $api_key = UdoitUtils::instance()->getValidRefreshedApiKey(MY_USER_ID);

    $search_q = [
        //'enrollment_term_id' => 236, // 2020 Fall UG
        //'enrollment_term_id' => 272, // 2021 Fall UG
        'enrollment_term_id' => 1, // default term
        //'blueprint' => false,
        //'starts_before' => strtotime('2020-08-01'),
        //'account_id' => 132 // TCH LMS > Institutional Studies
        'account_id' => 5 // Wichita
    ];
    if ($argc > 1) {
        $search_q['search_term'] = $search;
    }

    $courses = Udoit::searchCoursesByAccount($api_key, MY_BASE_URL, $search_q);
    $n_counter = 0;
    foreach ($courses as $course)
    {
        $job_id = queue_job($course);

        fputcsv(STDOUT, [
            $course->course_code,
            $course->id,
            $job_id,
            $course->name,
        ]);

        $n_counter += 1;
        if ($n_counter > 5) {
            break;
        }
    }

    return 0;
}

function queue_job(object $course) : string
{
    $job_id = UdoitJob::createJobGroupId();

    // all, errors, suggestions
    $report_type = 'all';

    $scan_items = [
        'announcements',
        'assignments',
        'discussions',
        'files',
        'pages',
        'syllabus',
        'module_urls',
    ];

    $common_data = [
        'base_uri'     => MY_BASE_URL,
        'course_title' => $course->name,
        'title'        => $course->name,
        'course_id'    => $course->id,
        'scan_item'    => null,
        'course_locale' => 'en',
        'report_type'  => $report_type,
        'flag'         => '1', // sourced from 'unpublished_flag' key in POST data
    ];

    // split up the items we're scanning into multiple jobs
    foreach ($scan_items as $scan_item) {
        $data = array_merge($common_data, ['scan_item' => $scan_item]);
        UdoitJob::addJobToQueue('scan', MY_USER_ID, $job_id, $data);
        traceln("QUEUE JOB", array(
            'job_group' => $job_id,
            'scan_item' => $scan_item,
            'id' => $course->id,
            'name' => $course->name,
        ));
    }

    return $job_id;
}

function traceln(string $msg = '', array $data)
{
    return errorln($msg . ' ' . json_encode($data));
}

function errorln(string $msg = '')
{
    return error($msg . "\n");
}

function error(string $msg)
{
    return fwrite(STDERR, $msg);
}

/*
    SAMPLE POST DATA FOR process.php

    array(8) {
      ["main_action"]=>
      string(5) "udoit"
      ["base_url"]=>
      string(35) "https://tabor.test.instructure.com/"
      ["content"]=>
      array(7) {
        [0]=>
        string(13) "announcements"
        [1]=>
        string(11) "assignments"
        [2]=>
        string(11) "discussions"
        [3]=>
        string(5) "files"
        [4]=>
        string(5) "pages"
        [5]=>
        string(8) "syllabus"
        [6]=>
        string(11) "module_urls"
      }
      ["course_id"]=>
      string(3) "146"
      ["context_label"]=>
      string(13) "ART351_MASTER"
      ["context_title"]=>
      string(39) "ART 351 - Issues in Fine Arts  (MASTER)"
      ["unpublished_flag"]=>
      string(1) "1"
      ["report_type"]=>
      string(3) "all"
    }
 */


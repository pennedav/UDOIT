<?php

if ($argc < 2) {
    die('expected: search string');
}

require_once 'config/settings.php';

$search = $argv[1];

$user_id = 4414;
$base_url = 'https://tabor.test.instructure.com';

$api_key = UdoitUtils::instance()->getValidRefreshedApiKey($user_id);

$r = Udoit::searchCourses($api_key, $base_url, $search);
//var_dump($r);

echo $r->raw_headers . "\n\n";
echo $r->raw_body . "\n";

exit;

$job_group_id = UdoitJob::createJobGroupId();

echo "job_group_id = '$job_group_id'\n";

//echo "Waiting..."; fgets(STDIN);

// $scan_item   string?
// $user_id     string
// $data        array

$user_id = 4414; // my user id from the udoit.users table not Canvas user id

$course_title = 'Sandbox - David P. AAA';
$base_url = 'https://tabor.test.instructure.com';
$title = 'Sandbox - David P. BBB';
$course_id = '6609';
$scan_item = 'pages';
$flag = '1'; // unpublished_flag from POST data
$report_type = 'all';
$course_locale = 'en';

$content = ['pages'];

$common_data = [
    'course_title' => $course_title,
    'base_uri'     => $base_url,
    'title'        => $title,
    'course_id'    => $course_id,
    'scan_item'    => null,//$scan_item,
    'course_locale' => $course_locale,
    'report_type'  => $report_type,
    'flag'         => $flag,
];

// split up the items we're scanning into multiple jobs
foreach ($content as $scan_item) {
    $data = array_merge($common_data, ['scan_item' => $scan_item]);
    UdoitJob::addJobToQueue('scan', $user_id, $job_group_id, $data);
    var_dump($data);
}

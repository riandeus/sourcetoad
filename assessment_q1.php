<?php
/**
 * ## Question 1
 *
 * Given the following example data structure. Write a single function to print out all its nested key value pairs
 * at any level for easy display to the user.
 */

$guests  = [
    [
        'guest_id' => 177,
        'guest_type' => 'crew',
        'first_name' => 'Marco',
        'middle_name' => null,
        'last_name' => 'Burns',
        'gender' => 'M',
        'guest_booking' => [
            [
                'booking_number' => 20008683,
                'ship_code' => 'OST',
                'room_no' => 'A0073',
                'start_time' => 1438214400,
                'end_time' => 1483142400,
                'is_checked_in' => true,
            ],
        ],
        'guest_account' => [
            [
                'account_id' => 20009503,
                'status_id' => 2,
                'account_limit' => 0,
                'allow_charges' => true,
            ],
        ],
    ],
    [
        'guest_id' => 10000113,
        'guest_type' => 'crew',
        'first_name' => 'Bob Jr ',
        'middle_name' => 'Charles',
        'last_name' => 'Hemingway',
        'gender' => 'M',
        'guest_booking' => [
            [
                'booking_number' => 10000013,
                'room_no' => 'B0092',
                'is_checked_in' => true,
            ],
        ],
        'guest_account' => [
            [
                'account_id' => 10000522,
                'account_limit' => 300,
                'allow_charges' => true,
            ],
        ],
    ],
    [
        'guest_id' => 10000114,
        'guest_type' => 'crew',
        'first_name' => 'Al ',
        'middle_name' => 'Bert',
        'last_name' => 'Santiago',
        'gender' => 'M',
        'guest_booking' => [
            [
                'booking_number' => 10000014,
                'room_no' => 'A0018',
                'is_checked_in' => true,
            ],
        ],
        'guest_account' => [
            [
                'account_id' => 10000013,
                'account_limit' => 300,
                'allow_charges' => false,
            ],
        ],
    ],
    [
        'guest_id' => 10000115,
        'guest_type' => 'crew',
        'first_name' => 'Red ',
        'middle_name' => 'Ruby',
        'last_name' => 'Flowers ',
        'gender' => 'F',
        'guest_booking' => [
            [
                'booking_number' => 10000015,
                'room_no' => 'A0051',
                'is_checked_in' => true,
            ],
        ],
        'guest_account' => [
            [
                'account_id' => 10000519,
                'account_limit' => 300,
                'allow_charges' => true,
            ],
        ],
    ],
    [
        'guest_id' => 10000116,
        'guest_type' => 'crew',
        'first_name' => 'Ismael ',
        'middle_name' => 'Jean-Vital',
        'last_name' => 'Jammes',
        'gender' => 'M',
        'guest_booking' => [
            [
                'booking_number' => 10000016,
                'room_no' => 'A0023',
                'is_checked_in' => true,
            ],
        ],
        'guest_account' => [
            [
                'account_id' => 10000015,
                'account_limit' => 300,
                'allow_charges' => true,
            ],
        ],
    ],
];

/**
 * @param $data
 * @param array $field - field from which we will start printing (for all guest entries)
 * @param string $line_prefix - prefix to indent with
 * @param int $indent_count
 * @param bool $key_is_found - indicates if key has already been found for this entry. Determines if we need to print or not.
 */
function print_data($data, $field = "", $line_prefix = "", $indent_count = 0, $key_is_found = false) {
    $key_found_this_iteration = false;
    if($field == "all") {
        $key_is_found = true; //print all fields in guest entry.
    }
    foreach($data as $key => $value) {
        if(!$key_is_found) {
            if($key === $field) {
                $key_is_found = true;
                $key_found_this_iteration = true;
            }
        }
        if(is_array($value)) {
            if(!is_numeric($key)) {
                if($key_is_found) {
                    echo str_repeat($line_prefix, $indent_count) . "$key:<br>\n";
                }
                print_data($value, $field, $line_prefix, $indent_count+1, $key_is_found);
            } else {
                print_data($value, $field, $line_prefix, $indent_count, $key_is_found);
            }
        } else {
            if($key_is_found) {
                echo str_repeat($line_prefix, $indent_count) . "$key: $value<br>\n";
            }
        }
        if($key_found_this_iteration) {
            $key_is_found = false; //exit out of nesting -- no need to print fields further outside of found key.
        }
    }
}

//print_r($guests);
$tests = ["first_name", "guest_booking", "room_no", "", "all"];

foreach($tests as $test) {
    if($test == "all") {
        echo "TEST: print out all fields per entry:<br>\n";
    } else {
        echo "TEST: print out field '$test' only (and subnested fields) in each entry: <br>\n";
    }
    switch ($test) {
        case "all":
            print_data($guests, "all", "-", 0);
            break;
        case "first_name":
            print_data($guests, "first_name", "-", 0);
            break;
        case "guest_booking":
            print_data($guests, "guest_booking", "-", 0);
            break;
        case "room_no":
            print_data($guests, "room_no", "-", 0);
            break;
        case "":
            echo "no test parameter sent in.";
            break;
        default:
            echo "unanticipated test parameter sent in.";
            break;
    }
    echo "<br>\n-------------------<br><br>\n\n";
}
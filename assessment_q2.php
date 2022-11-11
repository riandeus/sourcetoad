<?php
/**
 * ## Question 2
 *
 * Given the above example data structure again. Write a PHP function/method to sort the data structure
 * based on a key OR keys regardless of what level it or they occur within the data structure (i.e. sort by `last_name`
 * **AND** sort by `account_id`). **HINT**: Recursion is your friend.
 *
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

function multi_key_sort(&$data = [], $keys = [], $dir = []) {
    if (empty($data) or empty($keys)) {
        return $data;
    }

    $sort_cols = [];
    foreach($keys as $i => $key) {
        $col = get_nested_column($data, $key); //grab nested column per entry recursively.
        if(!empty($col)) {
            $sort_cols[$i] = $col;
        } else {
            unset($keys[$i]); //key was not found in data! don't sort by it.
            unset($dir[$i]);
        }
    }

    //pattern expected by array_multisort is column1_values, sort_order1, column2_vals, sort_order2, etc. and ending with $data at the end.
    $sort_args = [];
    foreach($keys as $i => $key) {
        $sort_args[] = $sort_cols[$i];
        if(isset($dir[$i]) and $dir[$i] == "desc") {
            $sort_args[] = SORT_DESC;
        } else {
            $sort_args[] = SORT_ASC; //default is sorting asc
        }
    }
    $sort_args[] = &$data;
    call_user_func_array("array_multisort", $sort_args); //sends an array of sort args to array_multisort(). This allows any number of keys to sort by.
    return $data;
}
function get_nested_column($data = [], $key = "", $result = []) {
    //echo "starting new fxn:\n";

    foreach($data as $arr_key => $arr_value) {
        //echo $arr_key . " " . $arr_value . "\n";
        if(is_array($arr_value)) {
            //echo "found array!";
            $result = get_nested_column($data[$arr_key], $key, $result);
            //echo "exiting recursive fxn:"; print_r($result);
            continue;
        } else if($key == $arr_key){
            //echo "found key!";
            $result[] = $arr_value;
            return $result;
        }
        //print_r($result);
    }

    return $result;
}
$sorted = multi_key_sort($guests, ["gender","abc","booking_number"], ["asc","asc","desc"]);
print_r($sorted);
<?php
// ------------------------------------------------------------------------------------------------
// This example shows how you can output the slot data in the JSON format.
//
// Webbrowser:
// 1. Copy the raidtrain/ directory to your webserver.
// 2. Navigate to this example page in a webbrowser.
//
// Commandline:
// 1. cd path/to/raidtrain/example.
// 2. php usage-json-output.php
// ------------------------------------------------------------------------------------------------


// Require/include the class file
require __DIR__.'/../raidtrain.php';


// Optionally aliasing the class so we don't have to refer to it as org\etrusci\raidtrain\RaidTrain
use org\etrusci\raidtrain\RaidTrain;


// Init the class
$App = new RaidTrain(
    name: 'Foo Event',
    time_zone: 'Europe/Zurich', # for valid timezone values see https://www.php.net/manual/en/timezones.php
    slot_file: './slot.txt',
);


// Get the slot data as json
$slot_data = $App->get_slot_data(as_json: true);


// Set output header
header('Content-Type: application/json; charset=utf-8');


// Print/output json slot data
print($slot_data);

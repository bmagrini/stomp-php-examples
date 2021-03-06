<?php
require __DIR__ . '/../vendor/autoload.php';
/**
 *
 * Copyright (C) 2009 Progress Software, Inc. All rights reserved.
 * http://fusesource.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Stomp\Client;
use Stomp\SimpleStomp;
use Stomp\Transport\Map;


// make a connection
$client = new Client('tcp://localhost:61613');
$stomp = new SimpleStomp($client);


// send a message to the queue
$body = array('city' => 'Belgrade', 'name' => 'Dejan');
$header = array();
$header['transformation'] = 'jms-map-json';
$mapMessage = new Map($body, $header);
$client->send('/queue/test', $mapMessage);
echo 'Sending array: ';
print_r($body);

$stomp->subscribe('/queue/test', 'transform-test', 'client', null, ['transformation' => 'jms-map-json']);
/** @var Map $msg */
$msg = $stomp->read();

// extract
if ($msg != null) {
    echo 'Received array: ';
    print_r($msg->map);
    // mark the message as received in the queue
    $stomp->ack($msg);
} else {
    echo "Failed to receive a message\n";
}

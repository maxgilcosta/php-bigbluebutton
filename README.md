# PHP BigBlueButton API Library

> BigBlueButton is an open source web conferencing system for on-line learning. – http://www.bigbluebutton.org

This is a php library to interface with a BigBlueButton server instance.

## Supported PHP-FIG Recommendations

  * PSR-1: Basic Coding Standard  
    http://www.php-fig.org/psr/psr-1/
  * PSR-2: Coding Style Guide  
    http://www.php-fig.org/psr/psr-2/
  * PSR-4: Improved Autoloading  
    http://www.php-fig.org/psr/psr-4/

## Installation

This package is [Composer](https://getcomposer.org/) compatible.
[Install Composer](https://getcomposer.org/) on your system to use it.
The run

    composer require sanduhrs/php-bigbluebutton

## Configuration

To get your API URL and secret login to your BigBlueButton server and run:

    $ bbb-conf --secret
           URL: http://example.org/bigbluebutton/
        Secret: aiShaiteih6nahchie1quaiyul8ce4Zu

## Usage

### Initialize a BigBlueButton object:

    <?php

    require_once 'vendor/autoload.php';

    use sanduhrs\BigBlueButton;

    $url = 'http://example.org/bigbluebutton/';
    $secret = 'aiShaiteih6nahchie1quaiyul8ce4Zu';
    $endpoint = 'api/';
    
    // Initialize a BigBlueButton object.
    $bbb = new BigBlueButton($url, $secret, $endpoint);

### Get the version of the remote server:

    $version = $bbb->server->getVersion();

### Add a meeting:

    $meeting = $bbb->server->addMeeting([
        'id' => '123-456-789-000',
        'attendeePW' => 'Guphei4i',
        'moderatorPW' => 'ioy9Xep9',
        'name' => 'A BigBlueButton meeting',
        'welcome' => 'Welcome to %%CONFNAME%%.',
        'logoutURL' => 'https://example.org/',
        'record' => true,
        'autoStartRecording' => true,
        'meta'  => [
            'bbb-recording-ready-url' => urlencode('https://example.com/api/v1/recording_status'),
            'presenter' => 'John Smith',
        ],
        //any other parameters from [BBB API Documentation](https://docs.bigbluebutton.org/dev/api.html#create)
    ]);

### Add a meeting with pre-uploaded slides:

    $meeting = [
        'id' => '123-456-789-001',
        'name' => 'A BigBlueButton meeting with custom slides',
    ];
    $meeting['slides'][] = new Document(
       'https://example.org/slide.png',
       'slide.png',
    );
    $meeting = $bbb->server->addMeeting($meeting);

### Get meeting join URL for a moderator:

    $full_name = 'Martin Moderator';
    $url = $meeting->join($full_name, true);

### Get meeting join URL for an attendee:

    $full_name = 'Anton Attendee';
    $url = $meeting->join($full_name);

## Full Usage Example

Initialize your project with Composer:

    composer init

Install this package and its dependencies:

    composer require sanduhrs/php-bigbluebutton

Copy this to a file called 'index.php', adjust the '$url' and '$secret' variables, then try out your setup:

    <?php
    
    require_once 'vendor/autoload.php';
    
    use sanduhrs\BigBlueButton;
    
    $url = 'http://example.org/bigbluebutton/';
    $secret = 'aiShaiteih6nahchie1quaiyul8ce4Zu';
    $endpoint = 'api/';
    
    // Initialize a BigBlueButton object.
    $bbb = new BigBlueButton($url, $secret, $endpoint);
    
    // Get the version of the remote server.
    $version = $bbb->server->getVersion();
    print "$version<br />\n";
    
    // Add a meeting.
    $meeting = $bbb->server->addMeeting([
        'id' => '123-456-789-000',
        'attendeePW' => 'Guphei4i',
        'moderatorPW' => 'ioy9Xep9',
        'name' => 'A BigBlueButton meeting',
        'welcome' => 'Welcome to %%CONFNAME%%.',
        'logoutURL' => 'https://example.org/',
        'record' => true,
        'autoStartRecording' => true,
        'meta'  => [
            'bn-recording-ready-url' => urlencode('https://example.com/api/v1/recording_status'),
            'presenter' => 'John Smith',
        ],
        //any other parameters from [BBB API Documentation](https://docs.bigbluebutton.org/dev/api.html#create)
    ]);
    print '<pre>' . print_r($meeting, true) . "</pre>\n\n";
    
    // Get meeting join URL for a moderator.
    $full_name = 'Martin Moderator';
    $url = $meeting->join($full_name, true);
    print "Hi $full_name, you are a moderator. Please join the call via $url<br />\n\n";
    
    // Get meeting join URL for an attendee:
    $full_name = 'Anton Attendee';
    $url = $meeting->join($full_name);
    print "Hi $full_name, you are an attendee. Please join the call via $url<br />\n\n";

## Bigbluebutton Secret and URI discovery

    bbb-conf --secret

## Tests

    export BBB_URI=http://example.org/bigbluebutton/
    export BBB_SECRET=aiShaiteih6nahchieq1quaiyul8ce4Zu
    export BBB_ENDPOINT=api/
    ./vendor/bin/phpunit

## License
GNU GENERAL PUBLIC LICENSE Version 3 and later

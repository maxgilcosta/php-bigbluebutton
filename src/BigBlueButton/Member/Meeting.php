<?php

namespace sanduhrs\BigBlueButton\Member;

use sanduhrs\BigBlueButton\Client;

/**
 * Class Meeting
 *
 * @package sanduhrs\BigBlueButton
 */
class Meeting
{
    /**
     * The meeting name.
     *
     * @var string
     */
    protected $meetingName;

    /**
     * The meeting ID.
     *
     * A meeting ID that can be used to identify this meeting by the 3rd-party
     * application.
     *
     * @var string
     */
    protected $meetingID;

    /**
     * The attendee password.
     *
     * The password that will be required for attendees to join the meeting.
     *
     * @var string
     */
    protected $attendeePW;

    /**
     * The moderator password.
     *
     * The password that will be required for moderators to join the meeting or
     * for certain administrative actions (i.e. ending a meeting).
     *
     * @var string
     */
    protected $moderatorPW;

    /**
     * The welcome string.
     *
     * @var string
     */
    protected $welcome;

    /**
     * The dial in number.
     *
     * @var string
     */
    protected $dialNumber;

    /**
     * The voice bridge.
     *
     * @var string
     */
    protected $voiceBridge;

    /**
     * The web voice.
     *
     * @var string
     */
    protected $webVoice;

    /**
     * The logout URL.
     *
     * @var string
     */
    protected $logoutURL;

    /**
     * The meeting will record.
     *
     * @var bool
     */
    protected $record;

    /**
     * The duration of the meeting.
     *
     * @var int
     */
    protected $duration;

    /**
     * Message for moderator only.
     *
     * @var string
     */
    protected $moderatorOnlyMessage;

    /**
     * Meeting automatically starts recording.
     *
     * @var bool
     */
    protected $autoStartRecording;

    /**
     * Meeting does allow to start/stop recording.
     *
     * @var bool
     */
    protected $allowStartStopRecording;

    /**
     * User shared webcams will only appear to moderators.
     *
     * @var bool
     */
    protected $webcamsOnlyForModerator;

    /**
     * URL to logo for the flash client.
     *
     * @var string
     */
    protected $logo;

    /**
     * Copyright statement for flash client.
     *
     * @var string
     */
    protected $copyright;

    /**
     * Mute on start.
     *
     * @var bool
     */
    protected $muteOnStart;

    // Read-only properties

    /**
     * The create time.
     *
     * @var integer
     */
    protected $createTime;

    /**
     * The create date.
     * @var string
     */
    protected $createDate;

    /**
     * The meeting is running.
     *
     * @var boolean
     */
    protected $running;

    /**
     * A user has already joined.
     *
     * @var boolean
     */
    protected $hasUserJoined;

    /**
     * The meeting is recording.
     *
     * @var boolean
     */
    protected $recording;

    /**
     * The meeting has been forcibly ended.
     *
     * @var boolean
     */
    protected $hasBeenForciblyEnded;

    /**
     * The start time.
     *
     * @var integer
     */
    protected $startTime;

    /**
     * The end time.
     *
     * @var int
     */
    protected $endTime;

    /**
     * The participant count.
     *
     * @var integer
     */
    protected $participantCount;

    /**
     * The listener count.
     *
     * @var int
     */
    protected $listenerCount;

    /**
     * The voice participant count.
     *
     * @var int
     */
    protected $voiceParticipantCount;

    /**
     * The video count.
     *
     * @var int
     */
    protected $videoCount;

    /**
     * The maximum of users.
     *
     * @var integer
     */
    protected $maxUsers;

    /**
     * The number of moderators.
     *
     * @var integer
     */
    protected $moderatorCount;

    /**
     * The attendees.
     *
     * @var array
     */
    protected $attendees;

    /**
     * Additional meta data.
     *
     * @var array
     */
    protected $metadata;

    /**
     * The recordings.
     *
     * @var array
     */
    protected $recordings;

    /**
     * The slides.
     *
     * @var array
     */
    protected $slides;

    /**
     * The BigBlueButton client.
     *
     * @var \sanduhrs\BigBlueButton\Client
     */
    protected $client;

    /**
     * Meeting constructor.
     *
     * @param array $attributes
     *   - id (string): The unique id for the meeting.
     *   - name (string): A name for the meeting.
     *   - welcome (string): A welcome message that gets displayed on the chat
     *     window when the participant joins. You can include keywords
     *     (%%CONFNAME%%, %%DIALNUM%%, %%CONFNUM%%) which will be substituted
     *     automatically. You can set a default welcome message on
     *     bigbluebutton.properties
     *   - dialNumber (string): The dial access number that participants can
     *     call in using regular phone. You can set a default dial number on
     *     bigbluebutton.properties
     *   - voiceBridge (string): Voice conference number that participants enter
     *     to join the voice conference. The default pattern for this is a
     *     5-digit number. This is the PIN that a dial-in user must enter to
     *     join the conference. If you want to change this pattern, you have to
     *     edit FreeSWITCH dialplan and defaultNumDigitsForTelVoice of
     *     bigbluebutton.properties. When using the default setup, we recommend
     *     you always pass a 5-digit voiceBridge parameter. Finally, if you
     *     don’t pass a value for voiceBridge, then users will not be able to
     *     join a voice conference for the session.
     *   - webVoice (string): Voice conference alphanumeric that participants
     *     enter to join the voice conference.
     *   - logoutURL (string): The URL that the BigBlueButton client will go to
     *     after users click the OK button on the ‘You have been logged out
     *     message’. This overrides, the value for
     *     bigbluebutton.web.loggedOutURL if defined in bigbluebutton.properties
     *   - record (boolean): Setting ‘record=true’ instructs the BigBlueButton
     *     server to record the media and events in the session for later
     *     playback. Available values are true or false. Default value is false.
     *   - duration (number): The maximum length (in minutes) for the meeting.
     *     Normally, the BigBlueButton server will end the meeting when either
     *     the last person leaves (it takes a minute or two for the server to
     *     clear the meeting from memory) or when the server receives an end API
     *     request with the associated meetingID (everyone is kicked and the
     *     meeting is immediately cleared from memory). BigBlueButton begins
     *     tracking the length of a meeting when the first person joins. If
     *     duration contains a non-zero value, then when the length of the
     *     meeting exceeds the duration value the server will immediately end
     *     the meeting (same as receiving an end API request).
     *   - meta (string): You can pass one or more metadata values for create a
     *     meeting. These will be stored by BigBlueButton and later retrievable
     *     via the getMeetingInfo call and getRecordings. Examples of meta
     *     parameters are meta_Presenter, meta_category, meta_LABEL, etc. All
     *     parameters are converted to lower case, so meta_Presenter would be
     *     the same as meta_PRESENTER.
     *   - moderatorOnlyMessage (string): Display a message to all moderators in
     *     the public chat.
     *   - autoStartRecording (boolean): Automatically starts recording when
     *     first user joins. NOTE: Don’t set to autoStartRecording =false and
     *     allowStartStopRecording=false as the user won’t be able to record.
     *   - allowStartStopRecording (boolean): Allow the user to start/stop
     *     recording. This means the meeting can start recording automatically
     *     (autoStartRecording=true) with the user able to stop/start recording
     *     from the client.
     *   - webcamsOnlyForModerator (boolean): Setting
     *     'webcamsOnlyForModerator=true' will cause all webcams shared by
     *     viewers during this meeting to only appear for moderators (added 1.1)
     *   - logo (string): Setting
     *     'logo=http://www.example.com/my-custom-logo.png' will replace the
     *     default logo in the Flash client. (added 2.0)
     *   - copyright (string): Setting 'copyright=My custom copyright' will
     *     replace the default copyright on the footer of the Flash client.
     *     (added 2.0)
     *   - muteOnStart (boolean): Setting 'muteOnStart=true' will mute all users
     *     when the meeting starts. (added 2.0)
     *
     * @param \sanduhrs\BigBlueButton\Client $client
     */
    public function __construct($attributes, Client $client)
    {
        $attributes += [
            'id' => '',
            'name' => '',
            'attendeePW' => '',
            'moderatorPW' => '',
            'welcome' => '',
            'dialNumber' => '',
            'voiceBridge' => '',
            'webVoice' => '',
            'logoutURL' => '',
            'record' => false,
            'duration' => 0,
            'meta' => [],
            'moderatorOnlyMessage' => '',
            'autoStartRecording' => false,
            'allowStartStopRecording' => true,
            'webcamsOnlyForModerator' => false,
            'logo' => '',
            'copyright' => '',
            'muteOnStart' => false,
        ];

        $this->attendees = [];
        $this->metadata = [];
        $this->recordings = [];
        $this->slides = [];

        // Take naming inconsistencies into account.
        if (!empty($attributes['name'])) {
            $attributes['meetingName'] = $attributes['name'];
            unset($attributes['name']);
        }
        if (!empty($attributes['id'])) {
            $attributes['meetingID'] = $attributes['id'];
            unset($attributes['id']);
        }
        if (!empty($attributes['meta'])) {
            $attributes['metadata'] = $attributes['meta'];
            unset($attributes['meta']);
        }

        foreach ($attributes as $key => $value) {
            if (property_exists(self::class, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->client = $client;
    }

    /**
     * Alias for getMeetingID().
     *
     * @return string
     */
    public function getId()
    {
        return $this->getMeetingID();
    }

    /**
     * Alias for setMeetingID().
     *
     * @param string $meetingID
     * @return Meeting
     */
    public function setId($meetingID)
    {
        return $this->setMeetingID($meetingID);
    }

    /**
     * Alias for getMeetingName().
     *
     * @return string
     */
    public function getName()
    {
        return $this->meetingName;
    }

    /**
     * Alias for setMeetingName().
     *
     * @param string $name
     * @return Meeting
     */
    public function setName($name)
    {
        $this->meetingName = $name;
        return $this;
    }

    /**
     * Get the meeting name.
     *
     * @return string
     */
    public function getMeetingName()
    {
        return $this->meetingName;
    }

    /**
     * Set the meeting name.
     *
     * @param string $name
     * @return Meeting
     */
    public function setMeetingName($name)
    {
        $this->meetingName = $name;
        return $this;
    }

    /**
     * Get the meeting id.
     *
     * @return string
     */
    public function getMeetingId()
    {
        return $this->meetingID;
    }

    /**
     * Set the meeting id.
     *
     * @param string $meetingID
     * @return Meeting
     */
    public function setMeetingId($meetingID)
    {
        $this->meetingID = $meetingID;
        return $this;
    }

    /**
     * Get the attendee password.
     *
     * @return string
     */
    public function getAttendeePw()
    {
        return $this->attendeePW;
    }

    /**
     * Set the attendee password.
     *
     * @param string $attendeePW
     * @return Meeting
     */
    public function setAttendeePw($attendeePW)
    {
        $this->attendeePW = $attendeePW;
        return $this;
    }

    /**
     * Get the moderator password.
     *
     * @return string
     */
    public function getModeratorPw()
    {
        return $this->moderatorPW;
    }

    /**
     * Set the moderator password.
     *
     * @param string $moderatorPW
     * @return Meeting
     */
    public function setModeratorPw($moderatorPW)
    {
        $this->moderatorPW = $moderatorPW;
        return $this;
    }

    /**
     * Get the welcome message.
     *
     * @return string
     */
    public function getWelcome()
    {
        return $this->welcome;
    }

    /**
     * Set the welcome message.
     *
     * @param string $welcome
     * @return Meeting
     */
    public function setWelcome($welcome)
    {
        $this->welcome = $welcome;
        return $this;
    }

    /**
     * Get the dial in number.
     *
     * @return string
     */
    public function getDialNumber()
    {
        return $this->dialNumber;
    }

    /**
     * Set the dial in number.
     *
     * @param string $dialNumber
     * @return Meeting
     */
    public function setDialNumber($dialNumber)
    {
        $this->dialNumber = $dialNumber;
        return $this;
    }

    /**
     * Get the voice bridge.
     *
     * @return string
     */
    public function getVoiceBridge()
    {
        return $this->voiceBridge;
    }

    /**
     * Set the voice bridge.
     *
     * @param string $voiceBridge
     * @return Meeting
     */
    public function setVoiceBridge($voiceBridge)
    {
        $this->voiceBridge = $voiceBridge;
        return $this;
    }

    /**
     * Get the web voice.
     *
     * @return string
     */
    public function getWebVoice()
    {
        return $this->webVoice;
    }

    /**
     * Set the web voice.
     *
     * @param string $webVoice
     * @return Meeting
     */
    public function setWebVoice($webVoice)
    {
        $this->webVoice = $webVoice;
        return $this;
    }

    /**
     * Get the logout URL.
     *
     * @return string
     */
    public function getLogoutURL()
    {
        return $this->logoutURL;
    }

    /**
     * Set the logout URL.
     *
     * @param string $logoutURL
     * @return Meeting
     */
    public function setLogoutURL($logoutURL)
    {
        $this->logoutURL = $logoutURL;
        return $this;
    }

    /**
     * Does the meeting record?
     *
     * @return boolean
     */
    public function getRecord()
    {
        return $this->doesRecord();
    }

    /**
     * Alias for getRecord().
     *
     * @return boolean
     */
    public function doesRecord()
    {
        return $this->record;
    }

    /**
     * Set the record.
     *
     * @param boolean $record
     * @return Meeting
     */
    public function setRecord($record)
    {
        $this->record = $record;
        return $this;
    }

    /**
     * Get the duration.
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set the duration.
     *
     * @param int $duration
     * @return Meeting
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Alias for getMetadata().
     *
     * @return array
     */
    public function getMeta()
    {
        return $this->getMetadata();
    }

    /**
     * Alias for setMetadata().
     *
     * @param array $metadata
     * @return Meeting
     */
    public function setMeta($metadata)
    {
        return $this->setMetadata($metadata);
    }

    /**
     * Set metadata.
     *
     * @param array $metadata
     * @return Meeting
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Get the moderator message.
     *
     * @return string
     */
    public function getModeratorOnlyMessage()
    {
        return $this->moderatorOnlyMessage;
    }

    /**
     * Set the moderator message.
     *
     * @param string $moderatorOnlyMessage
     * @return Meeting
     */
    public function setModeratorOnlyMessage($moderatorOnlyMessage)
    {
        $this->moderatorOnlyMessage = $moderatorOnlyMessage;
        return $this;
    }

    /**
     * Alias for getAutoStartRecording().
     *
     * @return boolean
     */
    public function doesAutoStartRecording()
    {
        return $this->getAutoStartRecording();
    }

    /**
     * Is the recording auto starting?
     *
     * @return boolean
     */
    public function getAutoStartRecording()
    {
        return $this->autoStartRecording;
    }

    /**
     * Set the auto start recording indicator.
     *
     * @param boolean $autoStartRecording
     * @return Meeting
     */
    public function setAutoStartRecording($autoStartRecording)
    {
        $this->autoStartRecording = $autoStartRecording;
        return $this;
    }

    /**
     * Alias for getAllowStartStopRecording().
     *
     * @return boolean
     */
    public function doesAllowStartStopRecording()
    {
        return $this->getAllowStartStopRecording();
    }

    /**
     * Does the meeting allow to start/stop recording?
     *
     * @return boolean
     */
    public function getAllowStartStopRecording()
    {
        return $this->allowStartStopRecording;
    }

    /**
     * Set the allow start/stop recording indicator.
     *
     * @param boolean $allowStartStopRecording
     * @return Meeting
     */
    public function setAllowStartStopRecording($allowStartStopRecording)
    {
        $this->allowStartStopRecording = $allowStartStopRecording;
        return $this;
    }

    /**
     * Get the create time.
     *
     * @return int
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Get the create date.
     *
     * @return string
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Has a user already joined?
     *
     * @return boolean
     */
    public function hasUserJoined()
    {
        return $this->hasUserJoined;
    }

    /**
     * Alias for getHasBeenForciblyEnded().
     *
     * @return boolean
     */
    public function hasBeenForciblyEnded()
    {
        return $this->getHasBeenForciblyEnded();
    }

    /**
     * Has the meeting been forcibly ended?
     *
     * @return boolean
     */
    public function getHasBeenForciblyEnded()
    {
        return $this->hasBeenForciblyEnded;
    }

    /**
     * Get the start time.
     *
     * @return int
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Get the end time.
     *
     * @return int
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Get the participant count.
     *
     * @return int
     */
    public function getParticipantCount()
    {
        return $this->participantCount;
    }

    /**
     * Get the listener count.
     *
     * @return int
     */
    public function getListenerCount()
    {
        return $this->listenerCount;
    }

    /**
     * Get the voice participant count.
     *
     * @return int
     */
    public function getVoiceParticipantCount()
    {
        return $this->voiceParticipantCount;
    }

    /**
     * Get the video count.
     *
     * @return int
     */
    public function getVideoCount()
    {
        return $this->videoCount;
    }

    /**
     * Get the max users.
     *
     * @return int
     */
    public function getMaxUsers()
    {
        return $this->maxUsers;
    }

    /**
     * Get the moderator count.
     *
     * @return int
     */
    public function getModeratorCount()
    {
        return $this->moderatorCount;
    }

    /**
     * Get the attendees.
     *
     * @return array
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * Get the meta data.
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Get the slides.
     *
     * @return array
     */
    public function getSlides()
    {
        return $this->slides;
    }

    /**
     * Add a document with slides.
     *
     * @param \sanduhrs\BigBlueButton\Member\Document $document
     */
    public function addSlides(Document $document)
    {
        $this->slides[] = $document;
    }

    /**
     * Set the client.
     *
     * @var \sanduhrs\BigBlueButton\Client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create meeting.
     *
     * Creates a BigBlueButton meeting.
     * The create call is idempotent: you can call it multiple times with the
     * same parameters without side effects. This simplifies the logic for
     * joining a user into a session, your application can always call create
     * before returning the join URL. This way, regardless of the order in which
     * users join, the meeting will always exist but won’t be empty. The
     * BigBlueButton server will automatically remove empty meetings that were
     * created but have never had any users after a number of minutes specified
     * by defaultMeetingCreateJoinDuration defined in bigbluebutton.properties.
     *
     * @return \sanduhrs\BigBlueButton\Member\Meeting
     *   The meeting object.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     *
     * @todo http://docs.bigbluebutton.org/dev/api.html#preupload-slides
     */
    public function create()
    {
        $response = $this->client->get('create', [
          'name' => $this->getName(),
          'meetingID' => $this->getMeetingID(),
          'attendeePW' => $this->getAttendeePW(),
          'moderatorPW' => $this->getModeratorPW(),
          'welcome' => $this->getWelcome(),
          'dialNumber' => $this->getDialNumber(),
          'logoutURL' => $this->getLogoutURL(),
          'record' => $this->getRecord(),
          'duration' => $this->getDuration(),
          'moderatorOnlyMessage' => $this->getModeratorOnlyMessage(),
          'autoStartRecording' => $this->getAutoStartRecording(),
          'allowStartStopRecording' => $this->getAllowStartStopRecording(),
        ]);
        $this->meetingID = $response->meetingID;
        return $this->getInfo();
    }

    /**
     * Join meeting.
     *
     * Joins a user to the meeting.
     *
     * @param string $fullName
     *   The full name that is to be used to identify this user to other
     *   conference attendees.
     * @param bool $moderator
     *   The role of the user is moderator.
     * @param array $options
     *   - createTime (string): Third-party apps using the API can now pass
     *     createTime parameter (which was created in the create call),
     *     BigBlueButton will ensure it matches the ‘createTime’ for the
     *     session. If they differ, BigBlueButton will not proceed with the join
     *     request. This prevents a user from reusing their join URL for a
     *     subsequent session with the same meetingID.
     *   - userID (string): An identifier for this user that will help your
     *     application to identify which person this is. This user ID will be
     *     returned for this user in the getMeetingInfo API call so that you can
     *     check.
     *   - webVoiceConf (string): If you want to pass in a custom
     *     voice-extension when a user joins the voice conference using voip.
     *     This is useful if you want to collect more info in you Call Detail
     *     Records about the user joining the conference. You need to modify
     *     your /etc/asterisk/bbb-extensions.conf to handle this new extensions.
     *   - configToken (string): The token returned by a setConfigXML API call.
     *     This causes the BigBlueButton client to load the config.xml
     *     associated with the token (not the default config.xml)
     *   - avatarURL (string): The link for the user’s avatar to be displayed
     *     when displayAvatar in config.xml is set to true.
     *   - redirect (string): The default behaviour of the JOIN API is to
     *     redirect the browser to the Flash client when the JOIN call succeeds.
     *     There have been requests if it’s possible to embed the Flash client
     *     in a “container” page and that the client starts as a hidden DIV tag
     *     which becomes visible on the successful JOIN. Setting this variable
     *     to FALSE will not redirect the browser but returns an XML instead
     *     whether the JOIN call has succeeded or not. The third party app is
     *     responsible for displaying the client to the user.
     *   - clientURL (string): Some third party apps what to display their own
     *     custom client. These apps can pass the URL containing the custom
     *     client and when redirect is not set to false, the browser will get
     *     redirected to the value of clientURL.
     *
     * @return string
     *   The meeting join URL to redirect to.
     */
    public function join($fullName, $moderator = false, $options = [])
    {
        $options += [
            'fullName' => $fullName,
            'meetingID' => $this->meetingID,
            'password' => $moderator ? $this->moderatorPW : $this->attendeePW,
        ];
        $uri = $this->client->generateURI('join', $options);
        return $uri;
    }

    /**
     * Alias for getRunning().
     *
     * @return boolean
     *   The running status of the meeting TRUE for running.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function isRunning()
    {
        return $this->getRunning();
    }

    /**
     * Check meeting status.
     *
     * This call enables you to simply check on whether or not a meeting is
     * running by looking it up with your meeting ID.
     *
     * @return boolean
     *   The running status of the meeting TRUE for running.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function getRunning()
    {
        $options = [
          'meetingID' => $this->meetingID,
        ];
        $response = $this->client->get('isMeetingRunning', $options);
        return (boolean) $response->running === 'true';
    }

    /**
     * End Meeting.
     *
     * Use this to forcibly end a meeting and kick all participants out of the
     * meeting.
     *
     * @return boolean
     *   The boolean TRUE if the end request has been sent.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function end()
    {
        $options = [
            'meetingID' => $this->meetingID,
            'password' => $this->moderatorPW,
        ];
        $this->client->get('end', $options);
        return true;
    }

    /**
     * Get Meeting Info
     *
     * This call will return all of a meeting’s information, including the list
     * of attendees as well as start and end times.
     *
     * @return \sanduhrs\BigBlueButton\Member\Meeting
     *   The meeting object.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    private function getInfo()
    {
        $options = [
            'meetingID' => $this->meetingID,
            'password' => $this->attendeePW,
        ];
        $response = $this->client->get('getMeetingInfo', $options);
        foreach ($response as $key => $value) {
            if (property_exists(self::class, $key)) {
                if ($key === 'attendees') {
                    foreach ($value as $attendee) {
                        $this->{$key}[$attendee->userID] = new Attendee(
                            (array) $attendee
                        );
                    }
                } elseif ($key === 'recordings') {
                    $this->recordings = (array) $value;
                } elseif ($key === 'metadata') {
                    $this->metadata = (array) $value;
                } elseif (is_array($this->{$key})) {
                    $this->{$key} = (array) $value;
                } else {
                    $this->{$key} = $value;
                }
            }
        }
        return $this;
    }

    /**
     * Get recording.
     *
     * Retrieves a recording that is available for playback.
     *
     * @param string $recordingID
     *   The recording ID.
     *
     * @return \sanduhrs\BigBlueButton\Member\Recording|FALSE
     *   An recording object or FALSE.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function getRecording($recordingID)
    {
        $recordings = $this->getRecordings([$recordingID]);
        if (isset($recordings[$recordingID])) {
            return $recordings[$recordingID];
        }
        return false;
    }

    /**
     * Get recordings.
     *
     * Retrieves the recordings that are available for playback.
     *
     * @param string[] $recordIds
     *   A record ID for get the recordings. It can be a set of recordIDs separate by commas. If the record ID is not
     *   specified, it will use meeting ID as the main criteria. If neither the meeting ID is specified, it will get ALL
     *   the recordings. The recordID can also be used as a wildcard by including only the first characters in the
     *   string.
     *
     * @param string[] $states
     *   Since version 1.0 the recording has an attribute that shows a state that Indicates if the recording is
     *   [processing|processed|published|unpublished|deleted]. The parameter state can be used to filter results. It can
     *   be a set of states separate by commas. If it is not specified only the states [published|unpublished] are
     *   considered (same as in previous versions). If it is specified as “any”, recordings in all states are included.
     *
     * @param string[] $meta
     *   You can pass one or more metadata values to filter the recordings returned. The format of these parameters is
     *   the same as the metadata passed to the create call. For more information see the docs for the create call.
     *   http://docs.bigbluebutton.org/dev/api.html#create
     *
     * @return array
     *   An array of \sanduhrs\BigBlueButton\Recording.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function getRecordings($recordIds = [], $states = ['published', 'unpublished'], $meta = [])
    {
        $options = [
            'meetingID' => $this->meetingID,
        ];

        if (count($recordIds)) {
            $options['recordID'] = implode(',', $recordIds);
        }

        if (count($states)) {
            $options['state'] = implode(',', $states);
        }

        if (count($meta)) {
            $options['meta'] = implode(',', $recordIds);
        }

        $response = $this->client->get('getRecordings', $options);

        $recordings = [];
        if (isset($response->recordings->recording) &&
            is_object($response->recordings->recording)) {
            $recordings[] = $response->recordings->recording;
        } elseif (isset($response->recordings->recording)) {
            $recordings = $response->recordings->recording;
        }
        foreach ($recordings as $recording) {
            $this->recordings[$recording->recordingID] = new Recording(
                (array) $recording
            );
            $this->recordings[$recording->recordingID]
              ->setClient($this->client);
        }
        return $this->recordings;
    }

    /**
     * Publish recording.
     *
     * Publish a recording for a given recordID.
     *
     * @param string $recordID
     *   A record ID to apply the publish action to.
     *
     * @return boolean
     *   The success status as TRUE.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function publishRecording($recordID)
    {
        return $this->publishRecordings([$recordID]);
    }

    /**
     * Publish recordings.
     *
     * Publish recordings for a set of record IDs.
     *
     * @param array $recordIDs
     *   A set of record IDs to apply the publish action to.
     *
     * @return boolean
     *   The success status as TRUE.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function publishRecordings($recordIDs)
    {
        $options = [
            'recordID' => implode(',', $recordIDs),
            'publish' => 'true',
        ];
        $this->client->get('publishRecordings', $options);
        return true;
    }

    /**
     * Delete recording.
     *
     * Delete a recording for a given recordID.
     *
     * @param string $recordID
     *   A record ID to apply the delete action to.
     *
     * @return boolean
     *   The success status as TRUE.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function deleteRecording($recordID)
    {
        return $this->deleteRecordings([$recordID]);
    }

    /**
     * Delete recordings.
     *
     * Delete recordings for a set of record IDs.
     *
     * @param array $recordIDs
     *   A set of record IDs to apply the delete action to.
     *
     * @return boolean
     *   The success status as TRUE.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function deleteRecordings($recordIDs)
    {
        $options = [
            'recordID' => implode(',', $recordIDs),
        ];
        $this->client->get('deleteRecordings', $options);
        return true;
    }

    /**
     * Set config xml.
     *
     * Associate a custom config.xml file with the current session. This call
     * returns a token that can later be passed as a parameter to a join URL.
     * When passed as a parameter, the BigBlueButton client will use the
     * associated config.xml for the user instead of using the default
     * config.xml. This enables 3rd party applications to provide user-specific
     * config.xml files.
     *
     * @param string $configXML
     *   A valid config.xml file.
     *
     * @return string
     *   The xml formatted server response string.
     */
    public function setConfigXML($configXML)
    {
        $options = [
            'meetingID' => $this->meetingID,
            'configXML' => $configXML,
        ];
        $this->client->postRaw('setConfigXML', $options);
        return true;
    }
}

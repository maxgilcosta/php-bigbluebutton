<?php

namespace sanduhrs\BigBlueButton;

use sanduhrs\BigBlueButton\Exception\BigBlueButtonException;
use sanduhrs\BigBlueButton\Member\Meeting;

/**
 * Class Server
 *
 * @package sanduhrs\BigBlueButton
 */
class Server
{
    /**
     * The server version.
     *
     * @var string
     */
    protected $version;

    /**
     * The meetings.
     *
     * @var array of \sanduhrs\BigBlueButton\Meeting
     */
    protected $meetings;

    /**
     * The recordings.
     *
     * @var array of \sanduhrs\BigBlueButton\Recording
     */
    protected $recordings;

    /**
     * The client.
     *
     * @var \sanduhrs\BigBlueButton\Client
     */
    protected $client;

    /**
     * Server constructor.
     *
     * @param \sanduhrs\BigBlueButton\Client $client
     */
    public function __construct(
        Client $client
    ) {
        $this->client = $client;
        $this->meetings = [];
        $this->recordings = [];
    }

    /**
     * Get version.
     *
     * Check the remote server for the BigBlueButton API version.
     *
     * @return string
     *   The version of the BigBlueButton server instance.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function getVersion()
    {
        $xml = $this->client->get('');
        $this->version = (string) $xml->version;
        return $this->version;
    }

    /**
     * Add meeting.
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
     * @param array $options
     *   - meetingID (string): The meeting ID.
     *   - attendeePW (string): The attendee password.
     *   - moderatorPW (string): The moderator password.
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
     *   - record (string): Setting ‘record=true’ instructs the BigBlueButton
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
     *   - webcamsOnlyForModerator
     *
     * @return \sanduhrs\BigBlueButton\Member\Meeting
     *   A meeting object.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function addMeeting($options)
    {
        $meeting = new Meeting($options, $this->client);
        return $meeting->create();
    }

    /**
     * Get meeting.
     *
     * This call will return a meeting from the list found on this server.
     *
     * @param string $meetingID
     *   The meeting ID.
     *
     * @return \sanduhrs\BigBlueButton\Member\Meeting|FALSE
     *   A meeting object or FALSE.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function getMeeting($meetingID)
    {
        $meetings = $this->getMeetings();
        if (isset($meetings[$meetingID])) {
            return $meetings[$meetingID];
        }
        return false;
    }

    /**
     * Get meetings.
     *
     * This call will return a list of all the meetings found on this server.
     *
     * @return array
     *   An array of \sanduhrs\BigBlueButton\Meetings.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function getMeetings()
    {
        $meetings = [];
        $response = $this->client->get('getMeetings');

        // No meetings were found on this server.
        if (!isset($response->meetings->meeting)) {
            return $meetings;
        }

        // Found one meeting, initialize it.
        if (is_object($response->meetings->meeting)) {
            $meetings[$response->meetings->meeting->meetingID] = new Meeting(
                (array) $response->meetings->meeting,
                $this->client
            );
            return $meetings;
        }

        // Found meetings, initialize'em.
        if (is_array($response->meetings->meeting)) {
            foreach ($response->meetings->meeting as $options) {
                $meetings[$options->meetingID] = new Meeting(
                    (array) $options,
                    $this->client
                );
            }
        }
        return $meetings;
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
        $recordings = $this->getRecordings([], [$recordingID]);
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
     * @param string[] $meetingIds
     *   A meeting ID for get the recordings. It can be a set of meetingIDs separate by commas. If the meeting ID is not
     *   specified, it will get ALL the recordings. If a recordID is specified, the meetingID is ignored.
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
     *   An array of \sanduhrs\BigBlueButton\Member\Recording.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function getRecordings($meetingIds = [], $recordIds = [], $states = ['published', 'unpublished'], $meta = [])
    {
        $recordings = [];
        $options = [];

        if (count($meetingIds)) {
            $options['meetingID'] = implode(',', $meetingIds);
        }

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

        // No meetings were found on this server.
        if (!isset($response->recordings->recording)) {
            return $recordings;
        }

        // Found one meeting, initialize it.
        if (is_object($response->meetings->meeting)) {
            $meetings[$response->meetings->meeting->meetingID] = new Meeting(
                (array) $response->meetings->meeting,
                $this->client
            );
            return $meetings;
        }

        // Found meetings, initialize'em.
        if (is_array($response->meetings->meeting)) {
            foreach ($response->meetings->meeting as $options) {
                $meetings[$options->meetingID] = new Meeting(
                    (array) $options,
                    $this->client
                );
            }
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
     * @param array $recordingIDs
     *   A set of record IDs to apply the publish action to.
     *
     * @return boolean
     *   The success status as TRUE.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function publishRecordings($recordingIDs)
    {
        $options = [
            'recordID' => implode(',', $recordingIDs),
            'publish' => 'true',
        ];
        $this->client->get('publishRecordings', $options);
        return true;
    }

    /**
     * Unpublish recording.
     *
     * Unpublish a recording for a given recordID.
     *
     * @param string $recordID
     *   A record ID to apply the unpublish action to.
     *
     * @return boolean
     *   The success status as TRUE.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function unpublishRecording($recordID)
    {
        return $this->unpublishRecordings([$recordID]);
    }

    /**
     * Unpublish recordings.
     *
     * Unpublish recordings for a set of record IDs.
     *
     * @param array $recordIDs
     *   A set of record IDs to apply the unpublish action to.
     *
     * @return boolean
     *   The success status as TRUE.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function unpublishRecordings($recordIDs)
    {
        $options = [
            'recordID' => implode(',', $recordIDs),
            'publish' => 'false',
        ];
        $this->client->get('unpublishRecordings', $options);
        return true;
    }

    /**
     * Update recording.
     *
     * Update a recording for a given recordID.
     *
     * @param string $recordID
     *   A record ID to apply the update action to.
     *
     * @return boolean
     *   The success status as TRUE.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function updateRecording($recordID)
    {
        return $this->updateRecordings([$recordID]);
    }

    /**
     * Update recordings.
     *
     * Update recordings for a set of record IDs.
     *
     * @param array $recordIDs
     *   A set of record IDs to apply the update action to.
     *
     * @return boolean
     *   The success status as TRUE.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function updateRecordings($recordIDs, $meta = [])
    {
        $options = [
            'recordID' => implode(',', $recordIDs),
        ];

        if (count($recordIDs)) {
            $options['recordID'] = implode(',', $recordIDs);
        }

        if (count($meta)) {
            $options['meta'] = implode(',', $meta);
        }

        $this->client->get('updateRecordings', $options);
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
     * Get default config xml.
     *
     * Retrieve the default config.xml. This call enables a 3rd party
     * application to get the current config.xml, modify it’s parameters, and
     * use setConfigXML to store it on the BigBlueButton server (getting a
     * reference token to the new config.xml), then using the token in as a
     * parameter in the join URL to override the default config.xml.
     *
     * @return string
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function getDefaultConfigXML()
    {
        $result = $this->client->getRaw('getDefaultConfigXML');
        $xml = simplexml_load_string($result);

        // Check for success
        if (!isset($xml->version)) {
            throw new BigBlueButtonException('Could not retrieve default config xml.');
        }
        return $result;
    }

    /**
     * Set default config xml.
     *
     * Associate a custom config.xml file with the current session. This call
     * returns a token that can later be passed as a parameter to a join URL.
     * When passed as a parameter, the BigBlueButton client will use the
     * associated config.xml for the user instead of using the default
     * config.xml. This enables 3rd party applications to provide user-specific
     * config.xml files.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function setDefaultConfigXML()
    {
        throw new BigBlueButtonException('Not implemented,yet.');
    }
}

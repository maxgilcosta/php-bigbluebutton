<?php

namespace sanduhrs\BigBlueButton\Member;

use sanduhrs\BigBlueButton\Client;

/**
 * Class Recording.
 *
 * @package sanduhrs\BigBlueButton
 */
class Recording
{
    /**
     * The recording id.
     *
     * @var string
     */
    protected $recordID;

    /**
     * The meeting id.
     *
     * @var string
     */
    protected $meetingID;

    /**
     * The record name.
     *
     * @var string
     */
    protected $name;

    /**
     * The published state.
     *
     * @var boolean
     */
    protected $published;

    /**
     * @var string
     */
    protected $state;

    /**
     * The start time.
     *
     * @var integer
     */
    protected $startTime;

    /**
     * The end time.
     *
     * @var integer
     */
    protected $endTime;

    /**
     * The metadata.
     *
     * @var array
     */
    protected $metadata;

    /**
     * The playback data.
     *
     * @var array
     */
    protected $playback;

    /**
     * The BigBlueButton client.
     *
     * @var \sanduhrs\BigBlueButton\Client
     */
    protected $client;

    /**
     * Recording constructor.
     *
     * @param array $attributes
     */
    public function __construct($attributes)
    {
        $attributes += [
            'recordID' => '',
            'meetingID' => '',
            'name' => '',
            'published' => '',
            'state' => '',
            'startTime' => '',
            'endTime' => '',
            'metadata' => [],
            'playback' => [],
        ];

        foreach ($attributes as $key => $value) {
            if (property_exists(self::class, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Get Record ID.
     *
     * @return string
     */
    public function getRecordID()
    {
        return $this->recordID;
    }

    /**
     * Get Meeting ID.
     *
     * @return string
     */
    public function getMeetingID()
    {
        return $this->meetingID;
    }

    /**
     * Get Name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Check if Recording is published.
     *
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Get published state.
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get Start Time.
     *
     * @return int
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Get End Time.
     *
     * @return int
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Get Metadata.
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Get the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->metadata['title'];
    }

    /**
     * Get the subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->metadata['subject'];
    }

    /**
     * Get the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->metadata['description'];
    }

    /**
     * Get the creator.
     *
     * @return string
     */
    public function getCreator()
    {
        return $this->metadata['creator'];
    }

    /**
     * Get the contributor.
     * @return string
     */
    public function getContributor()
    {
        return $this->metadata['contributor'];
    }

    /**
     * Get the language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->metadata['language'];
    }

    /**
     * Get Playback.
     *
     * @return array
     */
    public function getPlayback()
    {
        return $this->playback;
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
     * Publish recording.
     *
     * @return mixed
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function publish()
    {
        $options = [
            'recordID' => $this->recordID,
            'publish' => 'true',
        ];
        return $this->client->get('publishRecordings', $options);
    }

    /**
     * Unpublish recording.
     *
     * @return mixed
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function unpublish()
    {
        $options = [
            'recordID' => $this->recordID,
            'publish' => 'false',
        ];
        return $this->client->get('publishRecordings', $options);
    }

    /**
     * Delete recording.
     *
     * @return mixed
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     */
    public function delete()
    {
        $options = [
            'recordID' => $this->recordID,
        ];
        return $this->client->get('deleteRecordings', $options);
    }

    /**
     * Get recording text tracks.
     */
    public function getRecordingTextTracks()
    {
        $options = [
            'recordID' => $this->getRecordID(),
        ];
        $this->client->get('getRecordingTextTracks', $options);
    }

    /**
     * Put recording text tracks.
     */
    public function putRecordingTextTracks($kind = 'subtitles', $lang = 'en', $label = '')
    {
        $options = [
            'recordID' => $this->getRecordID(),
            'kind' => $kind,
            'lang' => $lang,
        ];
        $this->client->get('getRecordingTextTracks', $options);
    }
}

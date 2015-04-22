<?php namespace JobBrander\Jobs\Client\Providers;

use JobBrander\Jobs\Client\Job;

class Simplyhired extends AbstractProvider
{
    /**
     * Job provider
     *
     * @var string
     */
    protected $source = 'Simplyhired';

    /**
     * Developer Key
     *
     * @var string
     */
    protected $developerKey;

    /**
     * Client IP Address
     *
     * @var string
     */
    protected $ipAddress;

    /**
     * Configuration Flag
     *
     * @var string
     */
    protected $configFlag;

    /**
     * Search Style
     *
     * @var string
     */
    protected $searchStyle;

    /**
     * Description Fragment
     *
     * @var string
     */
    protected $descriptionFrag;

    /**
     * Returns the standardized job object
     *
     * @param array $payload
     *
     * @return \JobBrander\Jobs\Client\Job
     */
    public function createJobObject($payload)
    {
        $defaults = [
            'title',
            'company',
            'location',
            'latitude',
            'longitude',
            'date',
            'description',
            'url',
            'permalink',
        ];

        $payload = static::parseAttributeDefaults($payload, $defaults);

        $job = new Job([
            'title' => $payload['title'],
            'source' => $this->source,
            'dates' => $payload['date'],
            'description' => $payload['description'],
            'url' => $payload['url'],
        ]);

        $job->addCompanies($payload['company'])
            ->addLocations($payload['location'])
            ->addCodes($payload['permalink']);

        return $job;
    }

    /**
     * Get data format
     *
     * @return string
     */
    public function getFormat()
    {
        return 'json';
    }

    /**
     * Get listings path
     *
     * @return  string
     */
    public function getListingsPath()
    {
        return 'jobs';
    }

    /**
     * Get IP Address
     *
     * @return  string
     */
    public function getIpAddress()
    {
        if (isset($this->ipAddress)) {
            return $this->ipAddress;
        } else {
            return getHostByName(getHostName());
        }
    }

    /**
     * Get Search Style
     *
     * @return  string
     */
    public function getSearchStyle()
    {
        if (isset($this->searchStyle)) {
            return $this->searchStyle;
        } else {
            return '2';
        }
    }

    /**
     * Get Configuration Flag
     *
     * @return  string
     */
    public function getConfigFlag()
    {
        if (isset($this->configFlag)) {
            return $this->configFlag;
        } else {
            return 'r';
        }
    }

    /**
     * Get Description Fragment
     *
     * @return  string
     */
    public function getDescriptionFrag()
    {
        if (isset($this->descriptionFrag)) {
            return $this->descriptionFrag;
        } else {
            return 0; // By default, show the whole description
        }
    }

    /**
     * Get combined location
     *
     * @return string
     */
    public function getLocation()
    {
        $location = ($this->city ? $this->city.', ' : null).($this->state ?: null);

        if ($location) {
            return $location;
        }

        return null;
    }

    /**
     * Get parameters
     *
     * @return  array
     */
    public function getParameters()
    {
        return [];
    }

    /**
     * Get query string for client based on properties
     *
     * @return string
     */
    public function getQueryString()
    {
        $url_params = [
            'q' => 'getKeyword',
            'l' => 'getLocation',
            'ws' => 'getCount',
            'pn' => 'getPage',
        ];
        $query_params = [
            'auth' => 'getDeveloperKey',
            'clip' => 'getIpAddress',
            'ssty' => 'getSearchStyle',
            'cflg' => 'getConfigFlag',
            'frag' => 'getDescriptionFrag',
        ];

        $query_string = [];

        $url_string = $sep = '';
        array_walk($url_params, function ($value, $key) use (&$url_string, &$sep) {
            $computed_value = $this->$value();
            if (!is_null($computed_value)) {
                $url_string .= $sep . $key . '-' . urlencode($computed_value);
                $sep = '/';
            }
        });

        array_walk($query_params, function ($value, $key) use (&$query_string) {
            $computed_value = $this->$value();
            if (!is_null($computed_value)) {
                $query_string[$key] = $computed_value;
            }
        });
        return $url_string.'?'.http_build_query($query_string);
    }

    /**
     * Get url
     *
     * @return  string
     */
    public function getUrl()
    {
        $query_string = $this->getQueryString();

        return 'http://api.simplyhired.com/a/jobs-api/json/'.$query_string;
    }

    /**
     * Get http verb
     *
     * @return  string
     */
    public function getVerb()
    {
        return 'GET';
    }
}

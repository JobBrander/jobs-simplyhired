<?php namespace JobBrander\Jobs\Client\Providers;

use JobBrander\Jobs\Client\Job;

class Simplyhired extends AbstractProvider
{
    /**
     * Map of setter methods to query parameters
     *
     * @var array
     */
    protected $queryMap = [
        'setPshid' => 'pshid',
        'setAuth' => 'auth',
        'setSsty' => 'ssty',
        'setCflg' => 'cflg',
        'setClip' => 'clip',
        'setFrag' => 'frag',
        'setQ' => 'q',
        'setL' => 'l',
        'setMi' => 'mi',
        'setSb' => 'sb',
        'setWs' => 'ws',
        'setPn' => 'pn',
        'setSi' => 'si',
        'setFdb' => 'fdb',
        'setFjt' => 'fjt',
        'setFsr' => 'fsr',
        'setFem' => 'fem',
        'setFrl' => 'frl',
        'setFed' => 'fed',
        'setKeyword' => 'q',
        'setLocation' => 'l',
        'setCount' => 'ws',
    ];

    /**
     * Current api query parameters
     *
     * @var array
     */
    protected $queryParams = [
        'pshid' => null,
        'auth' => null,
        'ssty' => null,
        'cflg' => null,
        'clip' => null,
        'frag' => null,
    ];

    /**
     * Current api URL path parameters
     *
     * @var array
     */
    protected $urlParams = [
        'q' => null,
        'l' => null,
        'mi' => null,
        'sb' => null,
        'ws' => null,
        'pn' => null,
        'si' => null,
        'fdb' => null,
        'fjt' => null,
        'fsr' => null,
        'fem' => null,
        'frl' => null,
        'fed' => null,
    ];

    /**
     * Create new Simplyhired jobs client.
     *
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        array_walk($parameters, [$this, 'updateQuery']);
        // Set default parameters
        if (!isset($parameters['clip'])) {
            $this->updateQuery($this->getIpAddress(), 'clip');
        }
    }

    /**
     * Magic method to handle get and set methods for properties
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (isset($this->queryMap[$method], $parameters[0])) {
            $this->updateQuery($parameters[0], $this->queryMap[$method]);
        }
        return parent::__call($method, $parameters);
    }

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
        ];

        $payload = static::parseAttributeDefaults($payload, $defaults);

        $job = new Job([
            'title' => $payload['title'],
            'name' => $payload['title'],
            'description' => $payload['description'],
            'url' => $payload['url'],
            'location' => $payload['location'],
        ]);


        $location = static::parseLocation($payload['location']);

        $job->setCompany($payload['company'])
            ->setDatePostedAsString($payload['date']);

        if (isset($location[0])) {
            $job->setCity($location[0]);
        }
        if (isset($location[1])) {
            $job->setState($location[1]);
        }

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
     * Get IP Address
     *
     * @return  string
     */
    public function getIpAddress()
    {
        return getHostByName(getHostName());
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
     * Get query string for client based on properties
     *
     * @return string
     */
    public function getQueryString()
    {
        $query_string = [];
        $url_string = $sep = '';

        array_walk($this->urlParams, function ($value, $key) use (&$url_string, &$sep) {
            $computed_value = $value;
            if (!is_null($computed_value)) {
                $url_string .= $sep . $key . '-' . urlencode($computed_value);
                $sep = '/';
            }
        });

        array_walk($this->queryParams, function ($value, $key) use (&$query_string) {
            $computed_value = $value;
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

    /**
     * Attempts to update current query parameters.
     *
     * @param  string  $value
     * @param  string  $key
     *
     * @return Simplyhired
     */
    protected function updateQuery($value, $key)
    {
        if (array_key_exists($key, $this->queryParams)) {
            $this->queryParams[$key] = $value;
        }
        if (array_key_exists($key, $this->urlParams)) {
            $this->urlParams[$key] = $value;
        }
        return $this;
    }
}

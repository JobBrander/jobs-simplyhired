<?php namespace JobBrander\Jobs\Client\Providers\Test;

use JobBrander\Jobs\Client\Providers\Simplyhired;
use Mockery as m;

class SimplyhiredTest extends \PHPUnit_Framework_TestCase
{
    private $clientClass = 'JobBrander\Jobs\Client\Providers\AbstractProvider';
    private $collectionClass = 'JobBrander\Jobs\Client\Collection';
    private $jobClass = 'JobBrander\Jobs\Client\Job';

    public function setUp()
    {
        $this->params = [
            'auth' => uniqid(),
            'pshid' => uniqid()
        ];
        $this->client = new Simplyhired($this->params);
    }

    public function testItWillUseJsonFormat()
    {
        $format = $this->client->getFormat();

        $this->assertEquals('json', $format);
    }

    public function testItWillUseGetHttpVerb()
    {
        $verb = $this->client->getVerb();

        $this->assertEquals('GET', $verb);
    }

    public function testListingPath()
    {
        $path = $this->client->getListingsPath();

        $this->assertEquals('jobs', $path);
    }

    public function testItWillProvideEmptyParameters()
    {
        $parameters = $this->client->getParameters();

        $this->assertEmpty($parameters);
        $this->assertTrue(is_array($parameters));
    }

    public function testUrlIncludesKeywordWhenProvided()
    {
        $keyword = uniqid().' '.uniqid();
        $param = 'q-'.urlencode($keyword);

        $url = $this->client->setKeyword($keyword)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesKeywordWhenNotProvided()
    {
        $param = 'q-';

        $url = $this->client->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesLocationWhenCityAndStateProvided()
    {
        $city = uniqid();
        $state = uniqid();
        $param = 'l-'.urlencode($city.', '.$state);

        $url = $this->client->setLocation($city.', '.$state)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesLocationWhenCityProvided()
    {
        $city = uniqid();
        $param = 'l-'.urlencode($city);

        $url = $this->client->setLocation($city)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesLocationWhenStateProvided()
    {
        $state = uniqid();
        $param = 'l-'.urlencode($state);

        $url = $this->client->setLocation($state)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesLocationWhenNotProvided()
    {
        $param = 'l-';

        $url = $this->client->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesCountWhenProvided()
    {
        $count = uniqid();
        $param = 'ws-'.$count;

        $url = $this->client->setCount($count)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesCountWhenNotProvided()
    {
        $param = 'ws-';

        $url = $this->client->setCount(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesDeveloperKeyWhenProvided()
    {
        $param = 'auth='.$this->params['auth'];

        $url = $this->client->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesPageWhenProvided()
    {
        $page = uniqid();
        $param = 'pn-'.$page;

        $url = $this->client->setPn($page)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesPageWhenNotProvided()
    {
        $param = 'pn-';

        $url = $this->client->setPn(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesIpWhenProvided()
    {
        $ip = uniqid();
        $param = 'clip='.$ip;

        $url = $this->client->setClip($ip)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesIpWhenNotProvided()
    {
        $param = 'clip=';

        $url = $this->client->setClip(null)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesSearchStyleWhenProvided()
    {
        $ssty = uniqid();
        $param = 'ssty='.$ssty;

        $url = $this->client->setSsty($ssty)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesSearchStyleWhenNotProvided()
    {
        $param = 'ssty=';

        $url = $this->client->setSsty(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesConfigFlagWhenProvided()
    {
        $cflg = uniqid();
        $param = 'cflg='.$cflg;

        $url = $this->client->setCflg($cflg)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesConfigFlagWhenNotProvided()
    {
        $param = 'cflg=';

        $url = $this->client->setCflg(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesDescriptionFragWhenProvided()
    {
        $frag = uniqid();
        $param = 'frag='.$frag;

        $url = $this->client->setFrag($frag)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesDescriptionFragWhenNotProvided()
    {
        $param = 'frag=';

        $url = $this->client->setFrag(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testItCanCreateJobFromPayload()
    {
        $payload = $this->createJobArray();
        $results = $this->client->createJobObject($payload);

        $this->assertEquals($payload['title'], $results->title);
        $this->assertEquals($payload['description'], $results->description);
        $this->assertEquals($payload['company'], $results->company);
        $this->assertEquals($payload['url'], $results->url);
    }

    public function testItCanConnect()
    {
        $provider = $this->getProviderAttributes();

        for ($i = 0; $i < $provider['jobs_count']; $i++) {
            $payload['jobs'][] = $this->createJobArray();
        }

        $responseBody = json_encode($payload);

        $job = m::mock($this->jobClass);
        $job->shouldReceive('setQuery')->with($provider['keyword'])
            ->times($provider['jobs_count'])->andReturnSelf();
        $job->shouldReceive('setSource')->with($provider['source'])
            ->times($provider['jobs_count'])->andReturnSelf();

        $response = m::mock('GuzzleHttp\Message\Response');
        $response->shouldReceive('getBody')->once()->andReturn($responseBody);

        $http = m::mock('GuzzleHttp\Client');
        $http->shouldReceive(strtolower($this->client->getVerb()))
            ->with($this->client->getUrl(), $this->client->getHttpClientOptions())
            ->once()
            ->andReturn($response);
        $this->client->setClient($http);

        $results = $this->client->getJobs();

        $this->assertInstanceOf($this->collectionClass, $results);
        $this->assertCount($provider['jobs_count'], $results);
    }

    private function createJobArray() {
        return [
            'title' => uniqid(),
            'company' => uniqid(),
            'location' => uniqid(),
            'description' => uniqid(),
            'date' => '2015-07-'.rand(1,31),
            'url' => uniqid(),
        ];
    }

    private function getProviderAttributes($attributes = [])
    {
        $defaults = [
            'path' => uniqid(),
            'format' => 'json',
            'keyword' => uniqid(),
            'source' => uniqid(),
            'params' => [uniqid()],
            'jobs_count' => rand(2,10),

        ];
        return array_replace($defaults, $attributes);
    }
}

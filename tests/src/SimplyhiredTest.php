<?php namespace JobBrander\Jobs\Client\Providers\Test;

use JobBrander\Jobs\Client\Providers\Simplyhired;
use Mockery as m;

class SimplyhiredTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->params = [
            'developerKey' => '17a4c65cdfe9ad0e4dd622fe6612df0fc2cadb3c.101238'
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

        $url = $this->client->setCity($city)->setState($state)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesLocationWhenCityProvided()
    {
        $city = uniqid();
        $param = 'l-'.urlencode($city);

        $url = $this->client->setCity($city)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesLocationWhenStateProvided()
    {
        $state = uniqid();
        $param = 'l-'.urlencode($state);

        $url = $this->client->setState($state)->getUrl();

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
        $param = 'auth='.$this->params['developerKey'];

        $url = $this->client->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesDeveloperKeyWhenNotProvided()
    {
        $param = 'auth=';

        $url = $this->client->setDeveloperKey(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesPageWhenProvided()
    {
        $page = uniqid();
        $param = 'pn-'.$page;

        $url = $this->client->setPage($page)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesPageWhenNotProvided()
    {
        $param = 'pn-';

        $url = $this->client->setPage(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesIpWhenProvided()
    {
        $ip = uniqid();
        $param = 'clip='.$ip;

        $url = $this->client->setIpAddress($ip)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesIpWhenNotProvided()
    {
        $param = 'clip=';

        $url = $this->client->setIpAddress(null)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesSearchStyleWhenProvided()
    {
        $ssty = uniqid();
        $param = 'ssty='.$ssty;

        $url = $this->client->setSearchStyle($ssty)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesSearchStyleWhenNotProvided()
    {
        $param = 'ssty=';

        $url = $this->client->setSearchStyle(null)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesConfigFlagWhenProvided()
    {
        $cflg = uniqid();
        $param = 'cflg='.$cflg;

        $url = $this->client->setConfigFlag($cflg)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesConfigFlagWhenNotProvided()
    {
        $param = 'cflg=';

        $url = $this->client->setConfigFlag(null)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesDescriptionFragWhenProvided()
    {
        $frag = uniqid();
        $param = 'frag='.$frag;

        $url = $this->client->setDescriptionFrag($frag)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesDescriptionFragWhenNotProvided()
    {
        $param = 'frag=';

        $url = $this->client->setDescriptionFrag(null)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testItCanConnect()
    {
        $job_count = rand(2,10);
        $listings = ['jobs' => $this->createJobArray($job_count)];
        $source = $this->client->getSource();
        $keyword = 'project manager';

        $this->client->setKeyword($keyword)
            ->setCity('Chicago')
            ->setState('IL');

        $response = m::mock('GuzzleHttp\Message\Response');
        $response->shouldReceive($this->client->getFormat())->once()->andReturn($listings);

        $http = m::mock('GuzzleHttp\Client');
        $http->shouldReceive(strtolower($this->client->getVerb()))
            ->with($this->client->getUrl(), $this->client->getHttpClientOptions())
            ->once()
            ->andReturn($response);
        $this->client->setClient($http);

        $results = $this->client->getJobs();

        foreach ($listings['jobs'] as $i => $result) {
            $this->assertEquals($listings['jobs'][$i]['title'], $results->get($i)->title);
            $this->assertEquals($listings['jobs'][$i]['company'], $results->get($i)->company);
            $this->assertEquals($listings['jobs'][$i]['location'], $results->get($i)->location);
            $this->assertEquals($listings['jobs'][$i]['description'], $results->get($i)->description);
            $this->assertEquals($listings['jobs'][$i]['url'], $results->get($i)->url);
            $this->assertEquals($keyword, $results->get($i)->query);
            $this->assertEquals($source, $results->get($i)->source);
        }

        $this->assertEquals(count($listings['jobs']), $results->count());
    }

    private function createJobArray($num = 10) {
        $jobs = [];
        $i = 0;
        while ($i < 10) {
            $jobs[] = [
                'title' => uniqid(),
                'company' => uniqid(),
                'location' => uniqid(),
                'description' => uniqid(),
                'url' => uniqid(),
            ];
            $i++;
        }
        return $jobs;
    }
}

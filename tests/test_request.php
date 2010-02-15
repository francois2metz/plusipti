<?php

class TestRequest extends UnitTestCase
{
    public function setUp()
    {
        $_SERVER = array();
    }

    protected function setServerEnv($method, $original_uri = null, $http_accept = NULL)
    {
        $_SERVER = array('REQUEST_METHOD' => $method,
                         'REQUEST_URI' => $original_uri,
                         'HTTP_ACCEPT' => $http_accept);
    }

    protected function assertHttpMethod($good_method)
    {
        $this->setServerEnv($good_method);
        $request = new RequestMapper();
        $this->assertTrue($request->{'is'. ucfirst(strtolower($good_method))}, 'is'.ucfirst(strtolower($good_method)) .' should be true');
        $methods = $request->getPossibleRequestMethods();
        foreach ($methods as $method)
        {
            if ($method == $good_method)
                continue;
            $this->assertFalse($request->{'is'. ucfirst(strtolower($method))}, 'is'.ucfirst(strtolower($good_method)) .' should be false');
        }
        $this->assertEqual($good_method, $request->getRequestMethod());
        $this->assertEqual($good_method, $request->method);
    }

    public function testIsGet()
    {
        $this->assertHttpMethod('GET');
    }

    public function testIsPost()
    {
        $this->assertHttpMethod('POST');
    }

    public function testIsPut()
    {
        $this->assertHttpMethod('PUT');
    }

    public function testIsHead()
    {
        $this->assertHttpMethod('HEAD');
    }

    public function testIsDelete()
    {
        $this->assertHttpMethod('DELETE');
    }

    public function testIsOptions()
    {
        $this->assertHttpMethod('OPTIONS');
    }

    public function testIsTrace()
    {
        $this->assertHttpMethod('TRACE');
    }

    public function testIsConnect()
    {
        $this->assertHttpMethod('CONNECT');
    }

    public function testParseNoAcceptHttpHeader()
    {
        $this->setServerEnv('GET');
        $request = new RequestMapper();
        $this->assertNull($request->accept);
    }

    public function testParseAcceptHttpHeaderBuggy()
    {
        $this->setServerEnv('GET', null, 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        $request = new RequestMapper();
        $this->assertnotNull($request->accept);
        $this->assertEqual(2, count($request->accept));
        $this->assertEqual('text/html', $request->accept[0]);
    }

    public function testNotXhr()
    {
        $this->setServerEnv('GET');
        $request = new RequestMapper();
        $this->assertFalse($request->isXHR());
    }

    public function testIsXhr()
    {
        $this->setServerEnv('GET');
        $request = new RequestMapper();
        // need a way to mock http headers
        //        $this->assertTrue($request->isXHR());

    }

    public function testGetOriginalUrl()
    {
        $this->setServerEnv('GET', '/test');
        $request = new RequestMapper();
        $this->assertEqual('/test', $request->original_uri);
    }
}

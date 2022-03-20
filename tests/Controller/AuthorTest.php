<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorTest extends WebTestCase
{
    use \Baldeweg\Bundle\ApiBundle\ApiTestTrait;

    public function testScenario()
    {
        // find
        $request = $this->request('/api/author/find', 'GET', ['term' => 'name']);

        $this->assertIsArray($request);

        // new
        $request = $this->request('/api/author/new', 'POST', [], [
            'firstname' => 'Firstname',
            'surname' => 'Surname',
        ]);

        $this->assertIsInt($request->id);
        $this->assertEquals('Firstname', $request->firstname);
        $this->assertEquals('Surname', $request->surname);

        $id = $request->id;

        // edit
        $request = $this->request('/api/author/'.$id, 'PUT', [], [
            'firstname' => 'Firstname1',
            'surname' => 'Surname1',
        ]);

        $this->assertIsInt($request->id);
        $this->assertEquals('Firstname1', $request->firstname);
        $this->assertEquals('Surname1', $request->surname);

        // show
        $request = $this->request('/api/author/'.$id, 'GET');

        $this->assertIsInt($request->id);
        $this->assertEquals('Firstname1', $request->firstname);
        $this->assertEquals('Surname1', $request->surname);

        // delete
        $request = $this->request('/api/author/'.$id, 'DELETE');

        $this->assertEquals('The author was deleted successfully.', $request->msg);
    }
}

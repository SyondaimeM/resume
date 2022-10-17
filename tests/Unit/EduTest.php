<?php

namespace Tests\Unit;

use App\Http\Controllers\admin\EducationController;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

class EduTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testEduInsert()
    {
        $user = DB::table('users')->first();
        $request = Request::create('/education/store', 'POST',[
            'title' => 'title',
            'details' => 'details',
            'country' => 'country',
            'city' => 'city',
            'startDate' => 'startDate',
            'endDate' => 'endDate',
            'isActive' => 'isActive',
            'isShown' => 'isShown',
            'user_id' => $user->id,
            'collageName' => 'collageName',
            'degree' => 'degree',
            'department' => 'department',
            'gpa' => 'gpa',
        ]);
        $controller = new EducationController();
        $response = $controller->store($request);
        $this->assertEquals(302, $response->getStatusCode());
        //$this->assertTrue(true);
    }
}

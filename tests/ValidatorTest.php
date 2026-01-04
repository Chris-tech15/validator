<?php
require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Sielatchom\Validator\Validator;

final class ValidatorTest extends TestCase
{
    /** @test */
    public function it_validates_required_field(): void
    {
        $data = ['name' => ''];
        $validator = new Validator($data);
        $validator->validate(['name' => ['required']]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors());
    }

    /** @test */
    public function it_passes_required_field(): void
    {
        $data = ['name' => 'John'];
        $validator = new Validator($data);
        $validator->validate(['name' => ['required']]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_email(): void
    {
        $data = ['email' => 'invalid-email'];
        $validator = new Validator($data);
        $validator->validate(['email' => ['email']]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors());
    }

    /** @test */
    public function it_passes_email(): void
    {
        $data = ['email' => 'test@example.com'];
        $validator = new Validator($data);
        $validator->validate(['email' => ['email']]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_password_strength(): void
    {
        $data = ['password' => 'abc'];
        $validator = new Validator($data);
        $validator->validate(['password' => ['password:8,strong']]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_passes_password(): void
    {
        $data = ['password' => 'Abc123$%'];
        $validator = new Validator($data);
        $validator->validate(['password' => ['password:8,strong']]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_phone(): void
    {
        $data = ['phone' => '123'];
        $validator = new Validator($data);
        $validator->validate(['phone' => ['phone:9,13']]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_passes_phone(): void
    {
        $data = ['phone' => '237655123456'];
        $validator = new Validator($data);
        $validator->validate(['phone' => ['phone:9,13']]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_date(): void
    {
        $data = ['dob' => '2023-02-30'];
        $validator = new Validator($data);
        $validator->validate(['dob' => ['dateValidate']]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_passes_date(): void
    {
        $data = ['dob' => '2025-01-04'];
        $validator = new Validator($data);
        $validator->validate(['dob' => ['dateValidate']]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_integer(): void
    {
        $data = ['age' => 'abc'];
        $validator = new Validator($data);
        $validator->validate(['age' => ['integer']]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_passes_integer(): void
    {
        $data = ['age' => 25];
        $validator = new Validator($data);
        $validator->validate(['age' => ['integer']]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_boolean(): void
    {
        $data = ['active' => 'maybe'];
        $validator = new Validator($data);
        $validator->validate(['active' => ['boolean']]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_passes_boolean(): void
    {
        $data = ['active' => 1];
        $validator = new Validator($data);
        $validator->validate(['active' => ['boolean']]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_min_max(): void
    {
        $data = ['score' => 5];
        $validator = new Validator($data);
        $validator->validate(['score' => ['min:10', 'max:20']]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_passes_between(): void
    {
        $data = ['score' => 15];
        $validator = new Validator($data);
        $validator->validate(['score' => ['between:10,20']]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_in_rule(): void
    {
        $data = ['role' => 'guest'];
        $validator = new Validator($data);
        $validator->validate(['role' => ['in:admin,user']]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_passes_in_rule(): void
    {
        $data = ['role' => 'admin'];
        $validator = new Validator($data);
        $validator->validate(['role' => ['in:admin,user']]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_confirmed(): void
    {
        $data = ['password' => '123456', 'password_confirmation' => '654321'];
        $validator = new Validator($data);
        $validator->validate(['password' => ['confirmed']]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_passes_confirmed(): void
    {
        $data = ['password' => '123456', 'password_confirmation' => '123456'];
        $validator = new Validator($data);
        $validator->validate(['password' => ['confirmed']]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_regex(): void
    {
        $data = ['username' => 'john@123'];
        $validator = new Validator($data);
        $validator->validate(['username' => ['regex:/^[a-z0-9]+$/i']]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_passes_regex(): void
    {
        $data = ['username' => 'john123'];
        $validator = new Validator($data);
        $validator->validate(['username' => ['regex:/^[a-z0-9]+$/i']]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_url(): void
    {
        $data = ['website' => 'not-a-url'];
        $validator = new Validator($data);
        $validator->validate(['website' => ['url']]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_passes_url(): void
    {
        $data = ['website' => 'https://example.com'];
        $validator = new Validator($data);
        $validator->validate(['website' => ['url']]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_json(): void
    {
        $data = ['payload' => '{invalid-json}'];
        $validator = new Validator($data);
        $validator->validate(['payload' => ['json']]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_passes_json(): void
    {
        $data = ['payload' => '{"name":"John"}'];
        $validator = new Validator($data);
        $validator->validate(['payload' => ['json']]);

        $this->assertFalse($validator->fails());
    }
}

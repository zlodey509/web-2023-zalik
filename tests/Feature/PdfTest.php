<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PdfTest extends TestCase
{
    use WithoutMiddleware, DatabaseMigrations;

    protected $fileType = "pdf";
    protected $fields = ['id', 'filename', 'file_size'];
    protected $modelName = "file";


    public function testUploadOk()
    {
        Storage::fake('local');
        $fileName = Str::random() . "." .$this->fileType;
        $size =  random_int(1024,2048);
        $file = UploadedFile::fake()->create($fileName, $size);
        $response = $this->postJson(route("{$this->modelName}.store"), ["file" => $file]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => $this->fields
        ]);
        Storage::assertExists($fileName);
        $this->assertDatabaseHas(Str::plural($this->modelName), ['filename' => $fileName]);
    }

    public function testUploadBadFileType()
    {
        $file = UploadedFile::fake()->create("test.txt");
        $response = $this->postJson(route("{$this->modelName}.store"), ["file" => '']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->postJson(route("{$this->modelName}.store"), ["file" => $file]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    public function testUploadBEmptyRequest()
    {
        $file = UploadedFile::fake()->create("test.txt");
        $response = $this->postJson(route("{$this->modelName}.store"), ["file" => '']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->postJson(route("{$this->modelName}.store"), ["file" => $file]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
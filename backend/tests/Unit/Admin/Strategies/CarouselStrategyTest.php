<?php

namespace Tests\Unit\Admin\Strategies;

use App\Admin\Strategies\CarouselStrategy;
use App\Models\Carousel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CarouselStrategyTest extends TestCase
{
    use RefreshDatabase;

    private CarouselStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new CarouselStrategy();
    }

    public function test_it_returns_correct_keys_and_labels()
    {
        $this->assertEquals('carrossel', $this->strategy->key());
        $this->assertEquals('Slide', $this->strategy->singularLabel());
        $this->assertEquals('Carrossel', $this->strategy->pluralLabel());
    }

    public function test_it_validates_valid_data()
    {
        $request = new Request([
            'TITULO' => 'Test Slide',
            'DESCRICAO' => 'Test Desc',
            'IMG_DESKTOP_URL' => 'http://example.com/desktop.jpg',
            'IMG_MOBILE_URL' => 'http://example.com/mobile.jpg',
            'LINK_DESTINO' => 'http://example.com',
            'ORDEM' => 1,
            'ATIVO' => true,
        ]);

        $validated = $this->strategy->validateData($request);

        $this->assertEquals('Test Slide', $validated['TITULO']);
        $this->assertEquals('Test Desc', $validated['DESCRICAO']);
        $this->assertEquals(1, $validated['ORDEM']);
    }

    public function test_it_throws_validation_exception_for_invalid_data()
    {
        $request = new Request([
            'TITULO' => '', // Required
            'IMG_DESKTOP_URL' => 'not-a-url',
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->strategy->validateData($request);
    }

    public function test_it_creates_carousel_item()
    {
        $request = new Request();
        $data = [
            'TITULO' => 'New Slide',
            'IMG_DESKTOP_URL' => 'http://example.com/d.jpg',
            'IMG_MOBILE_URL' => 'http://example.com/m.jpg',
            'LINK_DESTINO' => 'http://example.com',
            'ORDEM' => 10,
            'ATIVO' => true,
        ];

        $model = $this->strategy->create($request, $data);

        $this->assertInstanceOf(Carousel::class, $model);
        $this->assertDatabaseHas('CARROSSEL', [
            'TITULO' => 'New Slide',
            'ORDEM' => 10,
            'ATIVO' => true,
        ]);
    }

    public function test_it_updates_carousel_item()
    {
        $model = Carousel::create([
            'TITULO' => 'Old Slide',
            'IMG_DESKTOP_URL' => 'http://example.com/d.jpg',
            'IMG_MOBILE_URL' => 'http://example.com/m.jpg',
            'LINK_DESTINO' => 'http://example.com',
            'ORDEM' => 1,
            'ATIVO' => false,
        ]);

        $request = new Request();
        $data = [
            'TITULO' => 'Updated Slide',
            'ATIVO' => true,
        ];

        $updated = $this->strategy->update($request, $model, $data);

        $this->assertEquals('Updated Slide', $updated->TITULO);
        $this->assertTrue($updated->ATIVO);
        $this->assertDatabaseHas('CARROSSEL', [
            'id' => $model->id,
            'TITULO' => 'Updated Slide',
            'ATIVO' => true,
        ]);
    }

    public function test_it_deletes_carousel_item()
    {
        $model = Carousel::create([
            'TITULO' => 'To Delete',
            'IMG_DESKTOP_URL' => 'http://example.com/d.jpg',
            'IMG_MOBILE_URL' => 'http://example.com/m.jpg',
            'LINK_DESTINO' => 'http://example.com',
            'ORDEM' => 1,
        ]);

        $this->assertDatabaseHas('CARROSSEL', ['id' => $model->id]);

        $request = new Request();
        $this->strategy->delete($request, $model);

        $this->assertDatabaseMissing('CARROSSEL', ['id' => $model->id]);
    }
}

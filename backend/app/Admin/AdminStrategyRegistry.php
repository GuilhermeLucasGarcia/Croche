<?php

namespace App\Admin;

use App\Admin\Strategies\AdminFormStrategy;
use App\Admin\Strategies\CategoryStrategy;
use App\Admin\Strategies\ProductStrategy;
use App\Admin\Strategies\UserStrategy;
use App\Admin\Strategies\CarouselStrategy;
use InvalidArgumentException;

class AdminStrategyRegistry
{
    public function get(string $key): AdminFormStrategy
    {
        return match ($key) {
            'produtos' => app(ProductStrategy::class),
            'categorias' => app(CategoryStrategy::class),
            'usuarios' => app(UserStrategy::class),
            'carrossel' => app(CarouselStrategy::class),
            default => throw new InvalidArgumentException("Estrategia nao encontrada: {$key}"),
        };
    }

    public function all(): array
    {
        return [
            $this->get('produtos'),
            $this->get('categorias'),
            $this->get('usuarios'),
            $this->get('carrossel'),
        ];
    }
}

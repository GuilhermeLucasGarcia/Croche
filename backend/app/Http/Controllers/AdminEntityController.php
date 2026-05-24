<?php

namespace App\Http\Controllers;

use App\Admin\AdminStrategyRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminEntityController extends Controller
{
    public function __construct(private readonly AdminStrategyRegistry $registry)
    {
    }

    public function index(Request $request, string $entity): View
    {
        $strategy = $this->registry->get($entity);
        $query = $strategy->listQuery($request);
        $items = $query->paginate(20)->withQueryString();

        return view('admin/index', [
            'strategy' => $strategy,
            'strategies' => $this->registry->all(),
            'items' => $items,
        ]);
    }

    public function create(Request $request, string $entity): View
    {
        $strategy = $this->registry->get($entity);

        return view('admin/form', [
            'strategy' => $strategy,
            'strategies' => $this->registry->all(),
            'mode' => 'create',
            'model' => null,
            'fields' => $strategy->fields($request, null),
        ]);
    }

    public function edit(Request $request, string $entity, string $id): View
    {
        $strategy = $this->registry->get($entity);
        $model = $strategy->load($request, $id);

        return view('admin/form', [
            'strategy' => $strategy,
            'strategies' => $this->registry->all(),
            'mode' => 'edit',
            'model' => $model,
            'fields' => $strategy->fields($request, $model),
        ]);
    }

    public function store(Request $request, string $entity): RedirectResponse
    {
        $strategy = $this->registry->get($entity);
        $data = $strategy->validateData($request, null);

        try {
            $model = $strategy->create($request, $data);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Erro ao salvar {$entity}: " . $e->getMessage(), ['exception' => $e]);
            return back()
                ->withInput()
                ->withErrors(['form' => 'Não foi possível salvar. Tente novamente.']);
        }

        return redirect()
            ->route('admin.edit', ['entity' => $strategy->key(), 'id' => $model->getKey()])
            ->with('status', $strategy->singularLabel().' cadastrado com sucesso.');
    }

    public function update(Request $request, string $entity, string $id): RedirectResponse
    {
        $strategy = $this->registry->get($entity);
        $model = $strategy->load($request, $id);
        $data = $strategy->validateData($request, $model);

        try {
            $strategy->update($request, $model, $data);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Erro ao atualizar {$entity}: " . $e->getMessage(), ['exception' => $e]);
            return back()
                ->withInput()
                ->withErrors(['form' => 'Não foi possível salvar. Tente novamente.']);
        }

        return back()->with('status', $strategy->singularLabel().' atualizado com sucesso.');
    }

    public function destroy(Request $request, string $entity, string $id): RedirectResponse
    {
        $strategy = $this->registry->get($entity);
        $model = $strategy->load($request, $id);

        try {
            $strategy->delete($request, $model);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Erro ao excluir {$entity}: " . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['form' => 'Não foi possível excluir. Tente novamente.']);
        }

        return redirect()->route('admin.index', ['entity' => $strategy->key()])
            ->with('status', $strategy->singularLabel() . ' excluído com sucesso.');
    }

    public function validateField(Request $request, string $entity): JsonResponse
    {
        $strategy = $this->registry->get($entity);
        $model = null;

        if ($request->filled('id')) {
            try {
                $model = $strategy->load($request, (string) $request->input('id'));
            } catch (\Throwable $e) {
                $model = null;
            }
        }

        try {
            $strategy->validateData($request, $model);
            return response()->json(['errors' => (object) []]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}

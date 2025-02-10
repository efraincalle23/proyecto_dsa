<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use Illuminate\Http\Request;


class EntidadController extends Controller
{
    public function indexEliminados()
    {
        $entidades = Entidad::where('eliminado', operator: false)->get();
        return view('entidades.index', compact('entidades'));
    }
    public function index(Request $request)
    {
        // Obtener todas las entidades con paginación
        $query = Entidad::query();

        // Filtrar por nombre
        if ($request->has('nombre') && $request->nombre != '') {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        // Filtrar por siglas
        if ($request->has('siglas') && $request->siglas != '') {
            $query->where('siglas', 'like', '%' . $request->siglas . '%');
        }

        // Filtrar por tipo
        if ($request->has('tipo') && $request->tipo != '') {
            $query->where('tipo', $request->tipo);
        }

        // Obtener entidades con paginación de 10 registros por página
        $entidades = $query->paginate(10);

        return view('entidades.index', compact('entidades'));
    }

    public function create()
    {
        $entidadesSuperiores = Entidad::where('eliminado', false)->get();
        return view('entidades.create', compact('entidadesSuperiores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:100',
            'siglas' => 'required|max:20',
            'tipo' => 'required',
            'entidad_superior_id' => 'nullable|exists:entidades,id',
        ]);

        Entidad::create($request->all());
        return redirect()->route('entidades.index')->with('success', 'Entidad creada con éxito.');
    }

    public function edit(Entidad $entidad)
    {
        $entidadesSuperiores = Entidad::where('eliminado', false)->get();
        return view('entidades.edit', compact('entidad', 'entidadesSuperiores'));
    }


    public function update(Request $request, $id)
    {
        $entidad = Entidad::findOrFail($id); // Encuentra la entidad por ID
        $request->validate([
            'nombre' => 'required|max:100',
            'siglas' => 'required|max:20',
            'tipo' => 'required',
            'entidad_superior_id' => 'nullable|exists:entidades,id',
        ]);
        $entidad->update($request->all());
        return redirect()->route('entidades.index')->with('success', 'Entidad actualizada con éxito.');
    }



    public function destroy($id)
    {
        $entidad = Entidad::findOrFail($id);
        $entidad->delete();

        return redirect()->route('entidades.index')->with('success', 'Entidad eliminada con éxito.');
    }
}
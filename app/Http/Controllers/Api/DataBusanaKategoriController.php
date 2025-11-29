<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataBusana;
use App\Models\DataBusanaKategori;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class DataBusanaKategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = DataBusanaKategori::query()->with('dataBusana');

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('nama_kategori', 'LIKE', "%{$search}%");
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $dataBusanaKategori = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Data busana kategori retrieved successfully',
                'data' => $dataBusanaKategori->items(),
                'pagination' => [
                    'current_page' => $dataBusanaKategori->currentPage(),
                    'last_page' => $dataBusanaKategori->lastPage(),
                    'per_page' => $dataBusanaKategori->perPage(),
                    'total' => $dataBusanaKategori->total(),
                    'from' => $dataBusanaKategori->firstItem(),
                    'to' => $dataBusanaKategori->lastItem(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data busana kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        if(!$request->nama_kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'nama_kategori is required',
            ], 422);
        }
        
        try {
            $dataBusanaKategori = DataBusanaKategori::where('nama_kategori', $request->nama_kategori)->first();

            if (!$dataBusanaKategori) {
                $validatedData = $request->validate([
                    'nama_kategori' => 'required|string|max:255|unique:data_busana_kategori,nama_kategori',
                ]);

                $dataBusanaKategori = DataBusanaKategori::create($validatedData);
            }

            if($request->id_busana) {
                $dataBusana = DataBusana::findOrFail($request->id_busana);
                if($dataBusana) {
                    $dataBusana->id_kategori = $dataBusanaKategori->id;
                    $dataBusana->save();
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Process successfully',
                'data' => $dataBusanaKategori
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create data busana kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $dataBusanaKategori = DataBusanaKategori::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Data busana kategori retrieved successfully',
                'data' => $dataBusanaKategori
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data busana kategori not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $dataBusanaKategori = DataBusanaKategori::findOrFail($id);

            $validatedData = $request->validate([
                'nama_kategori' => 'sometimes|required|string|max:255|unique:data_busana_kategori,nama_kategori,' . $id,
            ]);

            $dataBusanaKategori->update($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Data busana kategori updated successfully',
                'data' => $dataBusanaKategori
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update data busana kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $dataBusanaKategori = DataBusanaKategori::findOrFail($id);
            $dataBusanaKategori->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data busana kategori deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete data busana kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a simple listing without pagination for dropdown usage.
     */
    public function list(): JsonResponse
    {
        try {
            $kategoriBusana = DataBusanaKategori::select('id', 'nama_kategori')
                ->orderBy('nama_kategori', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data busana kategori list retrieved successfully',
                'data' => $kategoriBusana
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data busana kategori list',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
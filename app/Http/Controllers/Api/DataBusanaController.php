<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataBusana;
use App\Models\MasterTipeBusana;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class DataBusanaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = DataBusana::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_busana', 'LIKE', "%{$search}%")
                      ->orWhere('tipe_busana', 'LIKE', "%{$search}%")
                      ->orWhere('produk_by', 'LIKE', "%{$search}%");
                });
            }

            // Filter by tipe busana
            if ($request->has('id_tipe_busana') && !empty($request->id_tipe_busana)) {
                $query->where('id_tipe_busana', $request->id_tipe_busana);
            }

            // Filter by kategori
            if ($request->has('id_kategori') && !empty($request->id_kategori)) {
                $query->where('id_kategori', $request->id_kategori);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $dataBusana = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Data busana retrieved successfully',
                'data' => $dataBusana->items(),
                'pagination' => [
                    'current_page' => $dataBusana->currentPage(),
                    'last_page' => $dataBusana->lastPage(),
                    'per_page' => $dataBusana->perPage(),
                    'total' => $dataBusana->total(),
                    'from' => $dataBusana->firstItem(),
                    'to' => $dataBusana->lastItem(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data busana',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'id_tipe_busana' => 'required|exists:master_tipe_busana,id',
                'id_kategori' => 'nullable|integer',
                'nama_busana' => 'required|string|max:255',
                'harga_beli' => 'required|numeric|min:0',
                'tanggal_beli' => 'required|date',
                'produk_by' => 'nullable|string|max:255',
            ]);

            // Get tipe_busana from master_tipe_busana
            $masterTipeBusana = MasterTipeBusana::find($validatedData['id_tipe_busana']);
            $validatedData['tipe_busana'] = $masterTipeBusana->nama_tipe_busana;

            $dataBusana = DataBusana::create($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Data busana created successfully',
                'data' => $dataBusana
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
                'message' => 'Failed to create data busana',
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
            $dataBusana = DataBusana::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Data busana retrieved successfully',
                'data' => $dataBusana
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data busana not found',
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
            $dataBusana = DataBusana::findOrFail($id);

            $validatedData = $request->validate([
                'id_tipe_busana' => 'sometimes|required|exists:master_tipe_busana,id',
                'id_kategori' => 'nullable|integer',
                'nama_busana' => 'sometimes|required|string|max:255',
                'harga_beli' => 'sometimes|required|numeric|min:0',
                'tanggal_beli' => 'sometimes|required|date',
                'produk_by' => 'nullable|string|max:255',
            ]);

            // Get tipe_busana from master_tipe_busana if id_tipe_busana is being updated
            if (isset($validatedData['id_tipe_busana'])) {
                $masterTipeBusana = MasterTipeBusana::find($validatedData['id_tipe_busana']);
                $validatedData['tipe_busana'] = $masterTipeBusana->nama_tipe_busana;
            }

            $dataBusana->update($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Data busana updated successfully',
                'data' => $dataBusana
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
                'message' => 'Failed to update data busana',
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
            $dataBusana = DataBusana::findOrFail($id);
            $dataBusana->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data busana deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete data busana',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
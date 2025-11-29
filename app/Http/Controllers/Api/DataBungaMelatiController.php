<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataBungaMelati;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class DataBungaMelatiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = DataBungaMelati::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('jenis', 'LIKE', "%{$search}%")
                      ->orWhere('bouquet', 'LIKE', "%{$search}%")
                      ->orWhere('rincian', 'LIKE', "%{$search}%");
                });
            }

            // Filter by jenis
            if ($request->has('jenis') && !empty($request->jenis)) {
                $query->where('jenis', 'LIKE', "%{$request->jenis}%");
            }

            // Filter by bouquet
            if ($request->has('bouquet') && !empty($request->bouquet)) {
                $query->where('bouquet', 'LIKE', "%{$request->bouquet}%");
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $dataBungaMelati = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Data bunga melati retrieved successfully',
                'data' => $dataBungaMelati->items(),
                'pagination' => [
                    'current_page' => $dataBungaMelati->currentPage(),
                    'last_page' => $dataBungaMelati->lastPage(),
                    'per_page' => $dataBungaMelati->perPage(),
                    'total' => $dataBungaMelati->total(),
                    'from' => $dataBungaMelati->firstItem(),
                    'to' => $dataBungaMelati->lastItem(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data bunga melati',
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
                'jenis' => 'required|string|max:255',
                'rincian' => 'required|string|min:10',
                'bouquet' => 'required|string|max:255',
            ]);

            $dataBungaMelati = DataBungaMelati::create($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Data bunga melati created successfully',
                'data' => $dataBungaMelati
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
                'message' => 'Failed to create data bunga melati',
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
            $dataBungaMelati = DataBungaMelati::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Data bunga melati retrieved successfully',
                'data' => $dataBungaMelati
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data bunga melati not found',
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
            $dataBungaMelati = DataBungaMelati::findOrFail($id);

            $validatedData = $request->validate([
                'jenis' => 'sometimes|required|string|max:255',
                'rincian' => 'sometimes|required|string|min:10',
                'bouquet' => 'sometimes|required|string|max:255',
            ]);

            $dataBungaMelati->update($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Data bunga melati updated successfully',
                'data' => $dataBungaMelati
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
                'message' => 'Failed to update data bunga melati',
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
            $dataBungaMelati = DataBungaMelati::findOrFail($id);
            $dataBungaMelati->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data bunga melati deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete data bunga melati',
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
            $bungaMelati = DataBungaMelati::select('id', 'jenis', 'bouquet')
                ->orderBy('jenis', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data bunga melati list retrieved successfully',
                'data' => $bungaMelati
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data bunga melati list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unique jenis for filter dropdown.
     */
    public function getJenisList(): JsonResponse
    {
        try {
            $jenisList = DataBungaMelati::select('jenis')
                ->distinct()
                ->orderBy('jenis', 'asc')
                ->pluck('jenis');

            return response()->json([
                'status' => 'success',
                'message' => 'Jenis bunga melati list retrieved successfully',
                'data' => $jenisList
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve jenis list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unique bouquet for filter dropdown.
     */
    public function getBouquetList(): JsonResponse
    {
        try {
            $bouquetList = DataBungaMelati::select('bouquet')
                ->distinct()
                ->orderBy('bouquet', 'asc')
                ->pluck('bouquet');

            return response()->json([
                'status' => 'success',
                'message' => 'Bouquet list retrieved successfully',
                'data' => $bouquetList
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve bouquet list',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
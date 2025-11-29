<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of members.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $coreTeam = $request->get('core_team');
        $search = $request->get('search');
        $jabatan = $request->get('jabatan');
        $keyName = $request->get('key_name');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $query = Member::with(['jobdescs']);
        
        // Filter by core team if specified
        if ($coreTeam !== null) {
            if ($coreTeam == '1' || $coreTeam === 'true') {
                $query->coreTeam();
            } else {
                $query->nonCoreTeam();
            }
        }
        
        // Search by name, alamat, or nomor_hp
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('alamat', 'like', '%' . $search . '%')
                  ->orWhere('nomor_hp', 'like', '%' . $search . '%');
            });
        }
        
        // Filter by jabatan
        if ($jabatan) {
            $query->where('jabatan', $jabatan);
        }
        
        // Filter by key_name
        if ($keyName) {
            $query->where('key_name', $keyName);
        }
        
        // Sorting - validate sort_by column
        $allowedSortColumns = ['id', 'nama', 'jabatan', 'is_core_team', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }
        
        $members = $query->paginate($perPage)->appends($request->query());
        
        return response()->json([
            'success' => true,
            'message' => 'Members retrieved successfully',
            'data' => $this->cleanPaginationUrls($members)
        ]);
    }

    /**
     * Helper function to remove base URL from pagination links.
     */
    private function cleanPaginationUrls($paginator)
    {
        $data = json_decode(json_encode($paginator), true);
        
        $data['first_page_url'] = $this->removeBaseUrl($data['first_page_url']);
        $data['last_page_url'] = $this->removeBaseUrl($data['last_page_url']);
        $data['next_page_url'] = $this->removeBaseUrl($data['next_page_url']);
        $data['prev_page_url'] = $this->removeBaseUrl($data['prev_page_url']);
        $data['path'] = '';
        
        foreach ($data['links'] as &$link) {
            $link['url'] = $this->removeBaseUrl($link['url']);
        }
        
        return $data;
    }

    /**
     * Helper function to remove base URL.
     */
    private function removeBaseUrl($url)
    {
        if ($url === null) {
            return null;
        }
        // Remove base URL and path, keep only query parameters
        return preg_replace('/^https?:\/\/[^?]+/', '', $url);
    }

    /**
     * Display the specified member.
     */
    public function show(string $id): JsonResponse
    {
        $member = Member::with(['jobdescs'])->find($id);
        
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Member retrieved successfully',
            'data' => $member
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Http\Requests\NoteRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Exception;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Note::where('user_id', auth()->id());

            // Filter berdasarkan tanggal
            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');

                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            $notes = $query->latest()->paginate(10);

            $meta = [];
            if ($notes->hasMorePages()) {
                $meta['next_page_url'] = $notes->nextPageUrl();
            }

            return ApiResponse::success(
                'Catatan berhasil diambil',
                200,
                NoteResource::collection($notes),
                $meta
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                'Gagal mengambil catatan',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }

    public function store(NoteRequest $request)
    {
        try {
            $note = Note::create([
                'uuid'    => (string) Str::uuid(),
                'title'   => $request->title,
                'content' => $request->content,
                'user_id' => auth()->id(),
            ]);

            return ApiResponse::success(
                'Catatan berhasil dibuat',
                201,
                new NoteResource($note)
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                'Gagal membuat catatan',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }

    public function show($uuid)
    {
        try {
            $note = Note::findOrFail($uuid);
            $this->authorizeOwner($note);

            return ApiResponse::success(
                'Catatan berhasil diambil',
                200,
                new NoteResource($note)
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                'Gagal mengambil catatan',
                404,
                ['error' => 'Catatan yang diminta tidak ditemukan']
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                'Gagal mengambil catatan',
                $e->getCode() === 403 ? 403 : 500,
                ['error' => $e->getMessage()]
            );
        }
    }

    public function update(NoteRequest $request, $uuid)
    {
        try {
            $note = Note::findOrFail($uuid);
            $this->authorizeOwner($note);

            $note->update($request->validated());

            return ApiResponse::success(
                'Catatan berhasil diperbarui',
                200,
                new NoteResource($note)
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                'Gagal memperbarui catatan',
                404,
                ['error' => 'Catatan yang diminta tidak ditemukan']
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                'Gagal memperbarui catatan',
                $e->getCode() === 403 ? 403 : 500,
                ['error' => $e->getMessage()]
            );
        }
    }

    public function delete($uuid)
    {
        try {
            $note = Note::findOrFail($uuid);
            $this->authorizeOwner($note);

            $note->delete();

            return ApiResponse::success('Catatan berhasil dihapus (soft delete)', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                'Gagal menghapus catatan',
                404,
                ['error' => 'Catatan yang diminta tidak ditemukan']
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                'Gagal menghapus catatan',
                $e->getCode() === 403 ? 403 : 500,
                ['error' => $e->getMessage()]
            );
        }
    }

    public function destroy($uuid)
    {
        try {
            $note = Note::withTrashed()->findOrFail($uuid);

            $this->authorizeOwner($note);

            if (!$note->trashed()) {
                return ApiResponse::error(
                    'Catatan harus dihapus (soft delete) terlebih dahulu sebelum dihapus permanen',
                    400
                );
            }

            $note->forceDelete();

            return ApiResponse::success('Catatan berhasil dihapus permanen', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                'Gagal menghapus catatan permanen',
                404,
                ['error' => 'Catatan yang diminta tidak ditemukan']
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                'Gagal menghapus catatan permanen',
                $e->getCode() === 403 ? 403 : 500,
                ['error' => $e->getMessage()]
            );
        }
    }

    public function restore($uuid)
    {
        try {
            $note = Note::onlyTrashed()->findOrFail($uuid);
            $this->authorizeOwner($note);

            $note->restore();

            return ApiResponse::success(
                'Catatan berhasil dipulihkan',
                200,
                new NoteResource($note)
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                'Catatan tidak ditemukan di tempat sampah',
                404,
                ['error' => 'Catatan yang diminta tidak ditemukan di tempat sampah']
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                'Gagal memulihkan catatan',
                $e->getCode() === 403 ? 403 : 500,
                ['error' => $e->getMessage()]
            );
        }
    }

    private function authorizeOwner(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            throw new Exception('Aksi tidak diizinkan.', 403);
        }
    }
}

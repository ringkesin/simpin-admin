<?php

namespace App\Http\Controllers\Api;

use App\Models\Master\RekeningKkba;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MasterRekeningKkbaController extends BaseController
{
    public function index(): JsonResponse
    {
        $rekening = RekeningKkba::query()
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('nama_bank')
            ->orderBy('nomor_rekening')
            ->get();

        return $this->sendResponse(
            ['rekening_kkba' => $rekening],
            'Data rekening KKBA berhasil diambil.'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang diberikan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $rekening = RekeningKkba::create($validator->validated());

        return response()->json([
            'success' => true,
            'data' => ['rekening_kkba' => $rekening],
            'message' => 'Rekening KKBA berhasil ditambahkan.',
        ], 201);
    }

    public function show(RekeningKkba $rekeningKkba): JsonResponse
    {
        return $this->sendResponse(
            ['rekening_kkba' => $rekeningKkba],
            'Data rekening KKBA berhasil diambil.'
        );
    }

    public function update(Request $request, RekeningKkba $rekeningKkba): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->rules($rekeningKkba));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang diberikan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $rekeningKkba->update($validator->validated());

        return $this->sendResponse(
            ['rekening_kkba' => $rekeningKkba->fresh()],
            'Rekening KKBA berhasil diperbarui.'
        );
    }

    public function destroy(RekeningKkba $rekeningKkba): JsonResponse
    {
        $rekeningKkba->delete();

        return $this->sendResponse([], 'Rekening KKBA berhasil dihapus.');
    }

    private function rules(?RekeningKkba $rekeningKkba = null): array
    {
        return [
            'nama_bank' => ['required', 'string', 'max:100'],
            'nomor_rekening' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'max:50',
                Rule::unique('p_rekening_kkba', 'nomor_rekening')
                    ->where(fn ($query) => $query->where('nama_bank', request('nama_bank')))
                    ->ignore($rekeningKkba?->id),
            ],
            'atas_nama' => ['required', 'string', 'max:150'],
            'is_active' => ['required', 'boolean'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }
}

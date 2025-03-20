<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends BaseController
{
    public function getSecureFile(Request $request, $filePath)
    {
        if (!$request->hasValidSignature()) {
            return $this->sendError('Expired', ['error' => 'Link telah expired'], 403);
        }

        if (!Storage::exists($filePath)) {
            return $this->sendError('File Not Found', ['error' => 'File tidak ditemukan.'], 404);
        }

        // Get file MIME type
        $mimeType = Storage::mimeType($filePath);

        try {
            // Attempt to stream the file
            return new StreamedResponse(function () use ($filePath) {
                $stream = Storage::readStream($filePath);
                fpassthru($stream);
                fclose($stream);
            }, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            ]);
        } catch (\Exception $e) {
            // If streaming fails, fallback to download
            return Storage::download($filePath);
        }

        // return Storage::download($filePath);
    }

    public function getLink(Request $request)
    {
        $validator = Validator::make($request->all(), ['path' => 'required|string|max:1024',],['path.required' => 'Path tidak boleh kosong',]);
        if ($validator->fails()) {
            return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
        }

        if (!Storage::exists($request->path)) {
            return $this->sendError('File Not Found', ['error' => 'File tidak ditemukan.'], 404);
        }

        $signedUrl = URL::temporarySignedRoute(
            'secure-file', // Route name
            now()->addMinutes(1), // Expiration time
            ['path' => $request->path] // File path parameter
        );

        if($signedUrl){
            return $this->sendResponse(
                ['signed_url' => $signedUrl], 
                'Link berhasil digenerate dan available dalam 1 menit'
            );
        }

        return $this->sendError('Oopsie, Terjadi kesalahan.', [], 500);
    }
}

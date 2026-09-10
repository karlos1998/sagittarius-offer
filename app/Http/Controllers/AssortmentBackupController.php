<?php

namespace App\Http\Controllers;

use App\Services\AssortmentBackupService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssortmentBackupController extends Controller
{
    public function store(AssortmentBackupService $service): JsonResponse
    {
        return response()->json($service->create(), 201);
    }

    public function download(string $backup, AssortmentBackupService $service): BinaryFileResponse
    {
        return response()->download($service->path($backup), 'assortment-'.$backup.'.zip');
    }

    public function restore(string $backup, AssortmentBackupService $service): JsonResponse
    {
        return response()->json($service->restore($backup));
    }
}

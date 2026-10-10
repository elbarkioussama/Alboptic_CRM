<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class PrescriptionFileController extends Controller
{
    /**
     * Download or view securely the attached prescription document.
     */
    public function show(Request $request, Prescription $prescription): BinaryFileResponse|Response
    {
        if (! $prescription->prescription_file_path) {
            abort(404, 'Aucun document numérisé n\'est associé à cette ordonnance.');
        }

        if (! Storage::disk('local')->exists($prescription->prescription_file_path)) {
            abort(404, 'Le fichier de l\'ordonnance est introuvable sur le serveur sécurisé.');
        }

        $path = Storage::disk('local')->path($prescription->prescription_file_path);
        $mimeType = Storage::disk('local')->mimeType($prescription->prescription_file_path) ?: 'application/octet-stream';
        $filename = 'Ordonnance_' . ($prescription->client->full_name ? str_replace(' ', '_', $prescription->client->full_name) : 'Client') . '_' . $prescription->prescription_date->format('Y-m-d') . '.' . pathinfo($path, PATHINFO_EXTENSION);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}

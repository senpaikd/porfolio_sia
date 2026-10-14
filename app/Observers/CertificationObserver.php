<?php

namespace App\Observers;

use App\Models\Certification;
use Illuminate\Support\Facades\Storage;

class CertificationObserver
{
    public function updated(Certification $certification): void
    {
        if ($certification->wasChanged('certificate_path')) {
            $this->delete($certification->getOriginal('certificate_path'));
        }
    }

    public function deleting(Certification $certification): void
    {
        $this->delete($certification->certificate_path);
    }

    private function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}

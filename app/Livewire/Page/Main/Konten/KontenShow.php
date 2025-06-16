<?php

namespace App\Livewire\Page\Main\Konten;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\ContentModels;

class KontenShow extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData;
    public $id;

    public function mount($id) {
        $this->titlePage = 'Detail Konten';
        $this->menuCode = 'konten';
        $this->breadcrumb = [
            ['link' => route('main.konten.list'), 'label' => 'Konten'],
            ['link' => route('main.konten.show', ['id' => $id]), 'label' => 'Show']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = ContentModels::find($id);
        $this->loadData = $data;
        $fileUrl = null;
        if ($this->loadData['thumbnail_path'] && Storage::disk('kkba_simpin')->exists($this->loadData['thumbnail_path'])) {
            $fileUrl = URL::temporarySignedRoute(
                'secure-file', // Route name
                now()->addMinutes(1), // Expiration time
                ['path' => $this->loadData['thumbnail_path']] // File path parameter
            );
        }
        $this->loadData['thumbnail_path'] = $fileUrl;
    }

    public function render()
    {
        return view('livewire.page.main.konten.konten-show')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}

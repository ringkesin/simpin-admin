<?php

namespace App\Livewire\Page\Main\Konten;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Livewire\WithFileUploads;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\ContentModels;
use App\Models\Master\ContentTypeModels;

class KontenCreate extends Component
{
    use MyAlert;
    use MyHelpers;
    use WithFileUploads;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;
    public $contentType = [];
    public $loadData = [];

    #component input
    public $p_content_type_id;
    public $content_text;
    public $content_title;
    public $thumbnail_path;
    public $valid_from;
    public $valid_to;

    public function mount() {
        $this->titlePage = 'Tambah Konten';
        $this->menuCode = 'konten';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Konten'],
            ['link' => route('main.konten.list'), 'label' => 'List'],
            ['link' => route('main.konten.create'), 'label' => 'Create']
        ];
        $this->getContentType();
    }

    public function getContentType() {
        $data = ContentTypeModels::all();
        $this->contentType = $data;
    }

    public function render()
    {
        return view('livewire.page.main.konten.konten-create')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }

    public function saveInsert() {
        $validated = $this->validate([
            'p_content_type_id' => 'required',
            'content_title' => 'required',
            'valid_from' => 'required|date',
        ], [
            'p_content_type_id' => 'Tipe Konten required',
            'content_title' => 'Judul Konten required',
            'valid_from.required' => 'Valid from required.',
            'valid_from.date' => 'Format Valid from must "yyyy/mm/dd".',
            'valid_to.date' => 'Format Valid until must "yyyy/mm/dd".',
        ]);

        if($this->valid_to == "") {
            $this->valid_to = null;
        }

        try {
            $file_thumbnail = '';
            if($this->thumbnail_path) {
                $file = $this->thumbnail_path;
                $file_thumbnail = $file->store('uploads/content', 'local');
            }

            $post = ContentModels::create([
                'p_content_type_id' => $this->p_content_type_id,
                'content_title' => $this->content_title,
                'content_text' => $this->content_text,
                'thumbnail_path' => $file_thumbnail,
                'valid_from' => $this->valid_from,
                'valid_to' => $this->valid_to,
            ]);

            if($post) {
                $redirect = route('main.konten.show', ['id' => $post]);
                $this->sweetalert([
                    'icon' => 'success',
                    'confirmButtonText' => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data Berhasil Disimpan !',
                    'redirectUrl' => $redirect
                ]);
            } else {
                $this->sweetalert([
                    'icon' => 'warning',
                    'confirmButtonText'  => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data gagal di update, coba kembali.',
                ]);
            }

        } catch (QueryException $e) {
            $textError = '';
            if($e->errorInfo[1] == 1062) {
                $textError = 'Data gagal di update karena duplikat data, coba kembali.';
            } else {
                $textError = 'Data gagal di update, coba kembali. Error : '.$e;
            }
            $this->sweetalert([
                'icon' => 'error',
                'confirmButtonText'  => 'Okay',
                'showCancelButton' => false,
                'text' => $textError,
            ]);
        }
    }
}

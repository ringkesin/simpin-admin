<?php

namespace App\Livewire\Page\Main\Konten;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Livewire\WithFileUploads;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;

use App\Models\Main\ContentModels;
use App\Models\Master\ContentTypeModels;

class KontenEdit extends Component
{
    use MyAlert;
    use MyHelpers;
    use WithFileUploads;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData = [];
    public $contentType = [];

    #component input
    public $id;
    public $p_content_type_id;
    public $content_text;
    public $content_title;
    public $thumbnail_path;
    public $thumbnail_path_old;
    public $valid_from;
    public $valid_to;

    public function mount($id) {
        $this->titlePage = 'Update Konten';
        $this->menuCode = 'konten';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Konten'],
            ['link' => route('main.konten.list'), 'label' => 'List'],
            ['link' => route('main.konten.show', ['id' => $id]), 'label' => 'Show'],
            ['link' => route('main.konten.edit', ['id' => $id]), 'label' => 'Edit']
        ];

        $this->id = $id;
        $this->getData($id);
        $this->getContentType();
    }

    public function getContentType() {
        $data = ContentTypeModels::all();
        $this->contentType = $data;
    }

    public function getData($id) {
        $data = ContentModels::find($id);
        $this->loadData = $data;
        $this->p_content_type_id = $this->loadData['p_content_type_id'];
        $this->content_text = $this->loadData['content_text'];
        $this->content_title = $this->loadData['content_title'];
        $this->thumbnail_path = $this->loadData['thumbnail_path'];
        $this->thumbnail_path_old = $this->loadData['thumbnail_path'];
        $this->valid_from = $this->loadData['valid_from'];
        $this->valid_to = $this->loadData['valid_to'];
    }

    public function saveUpdate() {
        $validated = $this->validate([
            'p_content_type_id' => 'required',
            'content_title' => 'required',
            'valid_from' => 'required|date',
            'thumbnail_path' => 'nullable|file|mimes:jpg,png|max:2048'
        ], [
            'p_content_type_id' => 'Tipe Konten required',
            'content_title' => 'Judul Konten required',
            'valid_from.required' => 'Valid from required.',
            'valid_from.date' => 'Format Valid from must "yyyy/mm/dd".',
            'valid_to.date' => 'Format Valid until must "yyyy/mm/dd".',
            'thumbnail_path.file' => 'Format Thumbnail harus file.',
            'thumbnail_path.max' => 'Size Thumbnail Maximal 2 MB.'
        ]);

        if($this->valid_to == "") {
            $this->valid_to = null;
        }

        $redirect = route('main.konten.list');

        try {
            $file_thumbnail = '';
            if($this->thumbnail_path != $this->thumbnail_path_old) {
                $file = $this->thumbnail_path;
                $file_thumbnail = $file->store('uploads/content', 'kkba_simpin');
            } else {
                $file_thumbnail = $this->thumbnail_path_old;
            }

            $post = ContentModels::where('t_content_id', $this->id)->update([
                'p_content_type_id' => $this->p_content_type_id,
                'content_title' => $this->content_title,
                'content_text' => $this->content_text,
                'thumbnail_path' => $file_thumbnail,
                'valid_from' => $this->valid_from,
                'valid_to' => $this->valid_to,
            ]);

            if($post) {
                $redirect = route('main.konten.show', ['id' => $this->id]);
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

    public function render()
    {
        return view('livewire.page.main.konten.konten-edit')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}

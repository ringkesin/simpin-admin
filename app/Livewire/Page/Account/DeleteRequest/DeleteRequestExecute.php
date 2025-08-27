<?php

namespace App\Livewire\Page\Account\DeleteRequest;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

use App\Models\Main\DeleteAccountRequestModels;
use App\Models\User;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;

class DeleteRequestExecute extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData;
    public $id;

    public function mount($id) {
        $this->titlePage = 'Detail Permintaan Hapus Akun';
        $this->menuCode = 'delete_request_user';
        $this->breadcrumb = [
            ['link' => route('account.delete-request.list'), 'label' => 'Permintaan Hapus Akun'],
            ['link' => route('account.delete-request.execute', ['id' => $id]), 'label' => 'Detail']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $this->loadData = DeleteAccountRequestModels::with('user')
                                                    ->with('anggota')
                                                    ->where('t_delete_account_requests_id', $id)
                                                    ->first()->toArray();
        // dd($this->loadData);
    }

    public function approveSubmit()
    {
        try {
            DB::beginTransaction();

            $dataReq = DeleteAccountRequestModels::find($this->id);
            $dataReq->status = 'approved';
            $dataReq->save();

            $dataUser = User::find($this->loadData['user_id']);
            $dataUser->valid_until = now();
            $dataUser->save();

            DB::commit();

            $redirect = route('account.delete-request.list');
            $this->sweetalert([
                'icon' => 'success',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Data Berhasil Disimpan !',
                'redirectUrl' => $redirect
            ]);
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

    public function rejectSubmit()
    {
        try {
            DB::beginTransaction();

            $dataReq = DeleteAccountRequestModels::find($this->id);
            $dataReq->status = 'rejected';
            $dataReq->save();

            DB::commit();

            $redirect = route('account.delete-request.list');
            $this->sweetalert([
                'icon' => 'success',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Data Berhasil Disimpan !',
                'redirectUrl' => $redirect
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            $textError = 'Data gagal di update, coba kembali. Error: ' . $e->getMessage();

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
        return view('livewire.page.account.delete-request.delete-request-execute')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}

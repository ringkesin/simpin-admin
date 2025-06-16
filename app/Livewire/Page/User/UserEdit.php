<?php

namespace App\Livewire\Page\User;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Livewire\WithFileUploads;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Traits\MyAlert;
use App\Traits\MyParams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserEdit extends Component
{
    use MyAlert;
    use MyParams;
    use WithFileUploads;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $params;

    public $loadData;
    public $id;
    public $username;
    public $name;
    public $email;
    public $mobile;
    public $remarks;
    public $profile_photo_path;
    public $profile_photo;
    public $profile_photo_old;
    public $valid_from;
    public $valid_until;
    public $password;

    public function mount($id) {
        $this->titlePage = 'Update User';
        $this->menuCode = 'user';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'User'],
            ['link' => route('user.list'), 'label' => 'List'],
            ['link' => route('user.edit', ['id' => $id]), 'label' => 'Edit'],
        ];

        $this->id = $id;
        $this->params = $this->getParams([
            'users_form_avatar_maxsize',
            'users_form_avatar_allowed_filetype'
        ]);

        $this->getData($id);
    }

    public function getData($id) {
        $data = User::find($id);
        $this->loadData = $data;
        $this->username = $this->loadData['username'];
        $this->name = $this->loadData['name'];
        $this->email = $this->loadData['email'];
        $this->mobile = $this->loadData['mobile'];
        $this->remarks = $this->loadData['remarks'];
        $this->profile_photo_path = $this->loadData['profile_photo_path'];
        $this->profile_photo_old = $this->loadData['profile_photo_path'];
        $this->valid_from = $this->loadData['valid_from'];
        $this->valid_until = $this->loadData['valid_until'];
    }

    public function saveUpdate() {
        $validated = $this->validate(
            [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username,' . $this->id,
                'email' => 'required|string|email|max:255|unique:users,email,' . $this->id,
                'mobile' => 'required|min_digits:10|max:255|unique:users,mobile,' . $this->id,
                'valid_from' => 'required|date',
                'valid_until' => 'nullable|date|after_or_equal:valid_from',
                'remarks' => 'nullable|max:1024',
                'profile_photo' => 'nullable|' . $this->params['users_form_avatar_allowed_filetype'] . '|max:' . $this->params['users_form_avatar_maxsize'],
            ],
            ['mobile.min_digits' => 'The :attribute must be number with at least 10 digits.']
        );

        if($this->valid_until == "") {
            $this->valid_until = null;
        }

        $filename = 'avatar/blank-avatar.png';
        if ($validated['profile_photo'] != NULL) {
            $file = $validated['profile_photo'];
            $filename = time() . '_' . $file->getClientOriginalName();
            $location = 'avatar';
            $file->storeAs($location, $filename, 'kkba_simpin');
            // $file->move($location, $filename);
            $filename = $location . '/' . $filename;
            if ($this->profile_photo_old !== 'avatar/blank-avatar.png') {
                Storage::disk('kkba_simpin')->delete($this->profile_photo_old);
            }
            $this->profile_photo_path = $filename;
        }

        $jsonUpdate = [
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'valid_from' =>  $this->valid_from,
            'valid_until' => $this->valid_until,
            'remarks' => $this->remarks,
            'profile_photo_path' => $this->profile_photo_path,
            'updated_by' => Auth::id()
        ];

        if(!empty($this->password)) {
            $jsonUpdate['password'] = Hash::make($this->password);
        }

        $redirect = route('user.list');

        try {
            $post = User::where('id', $this->id)->update($jsonUpdate);

            if($post) {
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
                $textError = 'Data gagal di update, coba kembali.';
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
        return view('livewire.page.user.user-edit')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}

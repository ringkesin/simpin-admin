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

class UserCreate extends Component
{
    use MyAlert;
    use MyParams;
    use WithFileUploads;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $params;

    #component input
    public $username;
    public $name;
    public $email;
    public $mobile;
    public $remarks;
    public $profile_photo_path;
    public $profile_photo;
    public $valid_from;
    public $valid_until;
    public $password;

    public function mount() {
        $this->titlePage = 'Tambah User';
        $this->menuCode = 'user';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'User'],
            ['link' => route('user.list'), 'label' => 'List'],
            ['link' => route('user.create'), 'label' => 'Create']
        ];

        $this->params = $this->getParams([
            'users_form_avatar_maxsize',
            'users_form_avatar_allowed_filetype'
        ]);
    }

    public function saveInsert() {
        $validated = $this->validate(
            [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'mobile' => 'required|min_digits:10|max:255|unique:users',
                'password' => [
                    'required',
                    'min:8',
                    'regex:/[0-9]/'
                ],
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
            $file->storeAs($location, $filename, 'public');
            // $file->move($location, $filename);
            $filename = $location . '/' . $filename;
            $this->profile_photo_path = $filename;
        } else {
            $this->profile_photo_path = $filename;
        }

        $jsonUpdate = [
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'password' => Hash::make($this->password),
            'valid_from' =>  $this->valid_from,
            'valid_until' => $this->valid_until,
            'remarks' => $this->remarks,
            'profile_photo_path' => $this->profile_photo_path,
            'created_by' => Auth::id()
        ];

        $redirect = route('user.list');

        try {
            $post = User::create($jsonUpdate);

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
        return view('livewire.page.user.user-create')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}

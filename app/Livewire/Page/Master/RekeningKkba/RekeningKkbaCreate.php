<?php

namespace App\Livewire\Page\Master\RekeningKkba;

use App\Models\Master\RekeningKkba;
use App\Traits\MyAlert;
use Illuminate\Validation\Rule;
use Livewire\Component;

class RekeningKkbaCreate extends Component
{
    use MyAlert;

    public string $titlePage = 'Tambah Rekening KKBA';

    public string $menuCode = 'master-rekening-kkba';

    public array $breadcrumb = [];

    public string $nama_bank = '';

    public string $nomor_rekening = '';

    public string $atas_nama = '';

    public bool $is_active = true;

    public ?string $keterangan = null;

    public function mount(): void
    {
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.rekening-kkba.list'), 'label' => 'Rekening KKBA'],
            ['link' => null, 'label' => 'Tambah'],
        ];
    }

    public function saveInsert(): void
    {
        $validated = $this->validate($this->rules(), $this->messages());
        $rekening = RekeningKkba::create($validated);

        $this->sweetalert([
            'icon' => 'success',
            'confirmButtonText' => 'Okay',
            'showCancelButton' => false,
            'text' => 'Data berhasil disimpan.',
            'redirectUrl' => route('master.rekening-kkba.show', $rekening->id),
        ]);
    }

    private function rules(): array
    {
        return [
            'nama_bank' => ['required', 'string', 'max:100'],
            'nomor_rekening' => [
                'required', 'regex:/^[0-9]+$/', 'max:50',
                Rule::unique('p_rekening_kkba', 'nomor_rekening')
                    ->where(fn ($query) => $query->where('nama_bank', $this->nama_bank)),
            ],
            'atas_nama' => ['required', 'string', 'max:150'],
            'is_active' => ['boolean'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function messages(): array
    {
        return [
            'nama_bank.required' => 'Nama bank wajib diisi.',
            'nomor_rekening.required' => 'Nomor rekening wajib diisi.',
            'nomor_rekening.regex' => 'Nomor rekening hanya boleh berisi angka.',
            'nomor_rekening.unique' => 'Nomor rekening pada bank tersebut sudah terdaftar.',
            'atas_nama.required' => 'Nama pemilik rekening wajib diisi.',
        ];
    }

    public function render()
    {
        return view('livewire.page.master.rekening-kkba.rekening-kkba-create')
            ->layoutData([
                'title' => $this->titlePage,
                'breadcrumbs' => $this->breadcrumb,
                'menu_code' => $this->menuCode,
            ]);
    }
}

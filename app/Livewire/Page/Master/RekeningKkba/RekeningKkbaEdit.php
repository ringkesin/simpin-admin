<?php

namespace App\Livewire\Page\Master\RekeningKkba;

use App\Models\Master\RekeningKkba;
use App\Traits\MyAlert;
use Illuminate\Validation\Rule;
use Livewire\Component;

class RekeningKkbaEdit extends Component
{
    use MyAlert;

    public string $titlePage = 'Ubah Rekening KKBA';

    public string $menuCode = 'master-rekening-kkba';

    public array $breadcrumb = [];

    public int $id;

    public string $nama_bank = '';

    public string $nomor_rekening = '';

    public string $atas_nama = '';

    public bool $is_active = true;

    public ?string $keterangan = null;

    public function mount(int $id): void
    {
        $rekening = RekeningKkba::findOrFail($id);
        $this->id = $rekening->id;
        $this->nama_bank = $rekening->nama_bank;
        $this->nomor_rekening = $rekening->nomor_rekening;
        $this->atas_nama = $rekening->atas_nama;
        $this->is_active = $rekening->is_active;
        $this->keterangan = $rekening->keterangan;
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.rekening-kkba.list'), 'label' => 'Rekening KKBA'],
            ['link' => route('master.rekening-kkba.show', $id), 'label' => 'Detail'],
            ['link' => null, 'label' => 'Ubah'],
        ];
    }

    public function saveUpdate(): void
    {
        $rekening = RekeningKkba::findOrFail($this->id);
        $validated = $this->validate([
            'nama_bank' => ['required', 'string', 'max:100'],
            'nomor_rekening' => [
                'required', 'regex:/^[0-9]+$/', 'max:50',
                Rule::unique('p_rekening_kkba', 'nomor_rekening')
                    ->where(fn ($query) => $query->where('nama_bank', $this->nama_bank))
                    ->ignore($rekening->id),
            ],
            'atas_nama' => ['required', 'string', 'max:150'],
            'is_active' => ['boolean'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'nama_bank.required' => 'Nama bank wajib diisi.',
            'nomor_rekening.required' => 'Nomor rekening wajib diisi.',
            'nomor_rekening.regex' => 'Nomor rekening hanya boleh berisi angka.',
            'nomor_rekening.unique' => 'Nomor rekening pada bank tersebut sudah terdaftar.',
            'atas_nama.required' => 'Nama pemilik rekening wajib diisi.',
        ]);

        $rekening->update($validated);
        $this->sweetalert([
            'icon' => 'success',
            'confirmButtonText' => 'Okay',
            'showCancelButton' => false,
            'text' => 'Data berhasil diperbarui.',
            'redirectUrl' => route('master.rekening-kkba.show', $rekening->id),
        ]);
    }

    public function render()
    {
        return view('livewire.page.master.rekening-kkba.rekening-kkba-edit')
            ->layoutData([
                'title' => $this->titlePage,
                'breadcrumbs' => $this->breadcrumb,
                'menu_code' => $this->menuCode,
            ]);
    }
}

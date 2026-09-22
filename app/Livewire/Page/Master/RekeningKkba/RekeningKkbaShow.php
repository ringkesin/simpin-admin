<?php

namespace App\Livewire\Page\Master\RekeningKkba;

use App\Models\Master\RekeningKkba;
use App\Traits\MyAlert;
use Livewire\Component;

class RekeningKkbaShow extends Component
{
    use MyAlert;

    public string $titlePage = 'Detail Rekening KKBA';

    public string $menuCode = 'master-rekening-kkba';

    public array $breadcrumb = [];

    public int $id;

    public RekeningKkba $rekening;

    public function mount(int $id): void
    {
        $this->id = $id;
        $this->rekening = RekeningKkba::findOrFail($id);
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.rekening-kkba.list'), 'label' => 'Rekening KKBA'],
            ['link' => null, 'label' => 'Detail'],
        ];
    }

    public function delete(): void
    {
        $this->rekening->delete();
        $this->redirectRoute('master.rekening-kkba.list', navigate: true);
    }

    public function render()
    {
        return view('livewire.page.master.rekening-kkba.rekening-kkba-show')
            ->layoutData([
                'title' => $this->titlePage,
                'breadcrumbs' => $this->breadcrumb,
                'menu_code' => $this->menuCode,
            ]);
    }
}

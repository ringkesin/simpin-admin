import './bootstrap';
import 'preline';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Swal from 'sweetalert2';
window.Swal = Swal;

function initPreline() {
    try {
        window.HSStaticMethods.autoInit();
    } catch (error) {
        console.error('Error during preline autoInit:', error);
    }
}
document.addEventListener('DOMContentLoaded', function() {
    // console.log('DOM Loaded');
    initPreline();
});

document.addEventListener('livewire:navigated', () => {
    initPreline();
});

document.addEventListener('livewire:init', () => {
    Livewire.on('swal:modal', (event) => {
        Swal.fire({
            ...event[0], // Mengambil konfigurasi yang sudah ada
            preConfirm: () => {
                return new Promise((resolve, reject) => {
                    // Logika konfirmasi, misalnya tombol OK akan melanjutkan
                    resolve();
                });
            },
        }).then((result) => {
            if (result.isConfirmed) {
                // Aksi setelah konfirmasi, misalnya mengirimkan data atau melakukan tindakan lain
                if(event[0]['redirectUrl'] != '') {
                    window.location.href = event[0]['redirectUrl'];
                }
                // console.log(result);
            }
        });
    });
});

Livewire.start()

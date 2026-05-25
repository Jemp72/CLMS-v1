import Alpine from 'alpinejs';
import QrScanner from 'qr-scanner';

// Expose QR scanner globally so inline Alpine x-data in Blade can use it.
window.QrScanner = QrScanner;

window.Alpine = Alpine;
Alpine.start();

{{--
    Visitor Dashboard — redirect ke halaman utama publik.
    Halaman utama (index.blade.php) sudah menangani semua visitor
    termasuk yang sudah login. File ini di-forward saja.
--}}
@php
    // Redirect visitor yang sudah login ke halaman utama publik
    return redirect()->route('index');
@endphp

@extends('karyawan.master')

@section('title', 'Isi Kuisioner')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <a href="{{ route('kuisioner.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <div>
                    <h4 class="mb-0">{{ $periode->nama_periode }}</h4>
                    <p class="text-muted mb-0">{{ $pegawai->nama_pegawai }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('kuisioner.store', [$periode->id, $dinilai->id_user]) }}" method="POST">
        @csrf
        
        @foreach($kuisionerByKategori as $kategori => $kuisionerList)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ ucwords($kategori) }}</h5>
                </div>
                
                <div class="card-body">
                    @foreach($kuisionerList as $index => $kuisioner)
                        <div class="mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                            <label class="form-label fw-bold">
                                {{ $index + 1 }}. {{ $kuisioner->pertanyaan }}
                            </label>
                            
                            <div class="row g-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="col">
                                        <div class="form-check text-center">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="jawaban[{{ $kuisioner->id }}]" 
                                                   id="q{{ $kuisioner->id }}_{{ $i }}"
                                                   value="{{ $i }}"
                                                   {{ isset($existingAnswers[$kuisioner->id]) && $existingAnswers[$kuisioner->id] == $i ? 'checked' : '' }}
                                                   required>
                                            <label class="form-check-label d-block" for="q{{ $kuisioner->id }}_{{ $i }}">
                                                <div class="border rounded p-2">
                                                    <div class="fw-bold">{{ $i }}</div>
                                                    <small>
                                                        @switch($i)
                                                            @case(1) Sangat Buruk @break
                                                            @case(2) Buruk @break
                                                            @case(3) Cukup @break
                                                            @case(4) Baik @break
                                                            @case(5) Sangat Baik @break
                                                        @endswitch
                                                    </small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Actions -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-primary">Simpan Jawaban</button>
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                    </div>
                    <div>
                    <a href="{{ route('kuisioner.reset', [$periode->id, $dinilai->id_user]) }}" 
                    class="btn btn-outline-danger"
                    onclick="return confirm('Yakin ingin menghapus semua jawaban?')">
                    Hapus Semua
                    </a>


                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.form-check-input:checked + .form-check-label .border {
    background: #007bff;
    color: white;
    border-color: #007bff !important;
}

.form-check-label .border:hover {
    background: #f8f9fa;
    cursor: pointer;
}
</style>
@endsection
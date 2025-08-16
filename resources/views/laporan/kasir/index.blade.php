@extends('layouts.master')

@section('title')
    Laporan Kasir
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Laporan Kasir</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Generate Laporan Kasir</h3>
                </div>
                <div class="box-body">
                    <form action="{{ route('laporan.kasir.generate') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="kasir_id">Pilih Kasir</label>
                            <select name="kasir_id" id="kasir_id" class="form-control" required>
                                @foreach ($kasirs as $kasir)
                                    <option value="{{ $kasir->id }}">{{ $kasir->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="shift">Shift</label>
                            <select name="shift" id="shift" class="form-control" required>
                                <option value="1">Shift 1 (07:00 - 15:00)</option>
                                <option value="2">Shift 2 (15:00 - 23:00)</option>
                                <option value="3">Shift 3 (23:00 - 07:00)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Generate Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

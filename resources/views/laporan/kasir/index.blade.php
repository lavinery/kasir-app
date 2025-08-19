@extends('layouts.master')

@section('title')
    Laporan Kasir
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Laporan Kasir</li>
@endsection

@push('styles')
<style>
.date-range-container {
    display: flex;
    align-items: center;
    gap: 10px;
}
.date-range-container .form-control {
    flex: 1;
}
.date-range-separator {
    color: #666;
    font-weight: bold;
    white-space: nowrap;
}
@media (max-width: 768px) {
    .date-range-container {
        flex-direction: column;
        gap: 5px;
    }
    .date-range-separator {
        display: none;
    }
}
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Generate Laporan Kasir</h3>
                </div>
                <div class="box-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('laporan.kasir.generate') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kasir_id">Pilih Kasir</label>
                                    <select name="kasir_id" id="kasir_id" class="form-control" required>
                                        <option value="">-- Pilih Kasir --</option>
                                        @foreach ($kasirs as $kasir)
                                            <option value="{{ $kasir->id }}" {{ old('kasir_id') == $kasir->id ? 'selected' : '' }}>
                                                {{ $kasir->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shift">Shift</label>
                                    <select name="shift" id="shift" class="form-control" required>
                                        <option value="">-- Pilih Shift --</option>
                                        <option value="1" {{ old('shift') == '1' ? 'selected' : '' }}>
                                            Shift 1 (07:00 - 16:00)
                                        </option>
                                        <option value="2" {{ old('shift') == '2' ? 'selected' : '' }}>
                                            Shift 2 (16:00 - 22:00)
                                        </option>
                                        <option value="all" {{ old('shift') == 'all' ? 'selected' : '' }}>
                                            Semua Shift
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Periode Tanggal</label>
                            <div class="date-range-container">
                                <input type="date" name="tanggal_dari" id="tanggal_dari" class="form-control" 
                                       value="{{ old('tanggal_dari', date('Y-m-d')) }}" required
                                       placeholder="Tanggal Mulai">
                                <span class="date-range-separator">sampai</span>
                                <input type="date" name="tanggal_sampai" id="tanggal_sampai" class="form-control" 
                                       value="{{ old('tanggal_sampai', date('Y-m-d')) }}" required
                                       placeholder="Tanggal Akhir">
                            </div>
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i>
                                Pilih rentang tanggal untuk laporan. Maksimal 31 hari.
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-file-text-o"></i> Generate Laporan
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-default btn-block" onclick="resetForm()">
                                        <i class="fa fa-refresh"></i> Reset Form
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Date Buttons --}}
                        <div class="form-group">
                            <label>Quick Select:</label>
                            <div class="btn-group btn-group-sm" style="display: block;">
                                <button type="button" class="btn btn-default" onclick="setDateRange('today')">
                                    Hari Ini
                                </button>
                                <button type="button" class="btn btn-default" onclick="setDateRange('yesterday')">
                                    Kemarin
                                </button>
                                <button type="button" class="btn btn-default" onclick="setDateRange('thisWeek')">
                                    Minggu Ini
                                </button>
                                <button type="button" class="btn btn-default" onclick="setDateRange('lastWeek')">
                                    Minggu Lalu
                                </button>
                                <button type="button" class="btn btn-default" onclick="setDateRange('thisMonth')">
                                    Bulan Ini
                                </button>
                                <button type="button" class="btn btn-default" onclick="setDateRange('lastMonth')">
                                    Bulan Lalu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Set minimum date validation
    $('#tanggal_dari').on('change', function() {
        const fromDate = $(this).val();
        $('#tanggal_sampai').attr('min', fromDate);
        
        // Auto adjust end date if it's before start date
        const toDate = $('#tanggal_sampai').val();
        if (toDate && fromDate && new Date(toDate) < new Date(fromDate)) {
            $('#tanggal_sampai').val(fromDate);
        }
    });

    $('#tanggal_sampai').on('change', function() {
        const toDate = $(this).val();
        $('#tanggal_dari').attr('max', toDate);
        
        // Auto adjust start date if it's after end date
        const fromDate = $('#tanggal_dari').val();
        if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
            $('#tanggal_dari').val(toDate);
        }
    });

    // Form validation
    $('form').on('submit', function(e) {
        const fromDate = new Date($('#tanggal_dari').val());
        const toDate = new Date($('#tanggal_sampai').val());
        const diffTime = Math.abs(toDate - fromDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays > 31) {
            e.preventDefault();
            alert('Rentang tanggal maksimal 31 hari!');
            return false;
        }
    });
});

function setDateRange(period) {
    const today = new Date();
    let fromDate, toDate;

    switch(period) {
        case 'today':
            fromDate = toDate = today;
            break;
        case 'yesterday':
            fromDate = toDate = new Date(today.getTime() - 24 * 60 * 60 * 1000);
            break;
        case 'thisWeek':
            const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
            fromDate = startOfWeek;
            toDate = new Date();
            break;
        case 'lastWeek':
            const lastWeekStart = new Date(today.setDate(today.getDate() - today.getDay() - 7));
            const lastWeekEnd = new Date(today.setDate(today.getDate() - today.getDay() - 1));
            fromDate = lastWeekStart;
            toDate = lastWeekEnd;
            break;
        case 'thisMonth':
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
            toDate = new Date();
            break;
        case 'lastMonth':
            fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            toDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
    }

    $('#tanggal_dari').val(formatDate(fromDate));
    $('#tanggal_sampai').val(formatDate(toDate));
}

function formatDate(date) {
    return date.toISOString().split('T')[0];
}

function resetForm() {
    $('form')[0].reset();
    $('#tanggal_dari').val('{{ date("Y-m-d") }}');
    $('#tanggal_sampai').val('{{ date("Y-m-d") }}');
}
</script>
@endpush
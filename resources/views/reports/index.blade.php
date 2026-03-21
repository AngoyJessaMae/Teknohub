@extends('layouts.app')

@section('title', 'Reports - TeknoHub')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-chart-bar me-2"></i>Business Reports</h2>
    <div>
        <select id="monthSelect" class="form-select d-inline-block w-auto me-2">
            <option value="">All Months</option>
            <option value="current">Current Month</option>
        </select>
        <button class="btn btn-primary" onclick="loadReport()">Load Report</button>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Revenue Overview</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Requests Status</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Report Summary</h5>
            </div>
            <div class="card-body">
                <div id="reportSummary">
                    Select a month to view detailed reports.
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function loadReport() {
    const month = document.getElementById('monthSelect').value;
    // AJAX call to load report data
    fetch(`/admin/reports?month=${month}`)
        .then(response => response.json())
        .then(data => {
            updateCharts(data);
            updateSummary(data);
        });
}

function updateCharts(data) {
    // Update revenue chart
    // Update status chart
}

function updateSummary(data) {
    document.getElementById('reportSummary').innerHTML = `
        <p>Total Revenue: ₱${data.totalRevenue}</p>
        <p>Total Requests: ${data.totalRequests}</p>
        // etc
    `;
}
</script>
@endpush
@endsection

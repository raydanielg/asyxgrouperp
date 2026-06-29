@php
$title = 'Reports';
$description = 'Daily and weekly reception activity reports.';
@endphp
@extends('layouts.admin')
@section('title', $title)
@section('page_title', $title)
@section('content')
<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10">
        <h2 class="text-2xl font-bold">{{ $title }}</h2>
        <p class="text-emerald-100 text-sm mt-1">{{ $description }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border p-4 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">From</label>
            <input type="date" id="fromDate" value="{{ $from ?? now()->subDays(30)->toDateString() }}" class="px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">To</label>
            <input type="date" id="toDate" value="{{ $to ?? now()->toDateString() }}" class="px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
        <button onclick="loadReports()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">Generate Report</button>
    </div>
</div>

<div id="reportLoading" class="hidden text-center py-8 text-xs text-gray-500">Generating report...</div>

<div id="reportContent" class="space-y-6">
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase">Visitors</p>
            <p class="text-2xl font-bold text-gray-900 mt-1" id="totalVisitors">0</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase">Appointments</p>
            <p class="text-2xl font-bold text-gray-900 mt-1" id="totalAppointments">0</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase">Calls</p>
            <p class="text-2xl font-bold text-gray-900 mt-1" id="totalCalls">0</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase">Correspondence</p>
            <p class="text-2xl font-bold text-gray-900 mt-1" id="totalCorrespondence">0</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase">Parcels</p>
            <p class="text-2xl font-bold text-gray-900 mt-1" id="totalParcels">0</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase">Front Desk</p>
            <p class="text-2xl font-bold text-gray-900 mt-1" id="totalFrontDesk">0</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border p-4">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Visitors & Appointments by Day</h3>
            <div class="h-64"><canvas id="dailyChart"></canvas></div>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Calls by Status</h3>
            <div class="h-64"><canvas id="callsChart"></canvas></div>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Parcels by Status</h3>
            <div class="h-64"><canvas id="parcelsChart"></canvas></div>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Correspondence by Type</h3>
            <div class="h-64"><canvas id="correspondenceChart"></canvas></div>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-4">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Front Desk by Status</h3>
        <div class="h-64 max-w-2xl"><canvas id="frontDeskChart"></canvas></div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
let charts = {};

function formatLabel(dateString) {
    const d = new Date(dateString);
    return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
}

function destroyCharts() {
    Object.values(charts).forEach(c => c && c.destroy());
    charts = {};
    document.querySelectorAll('[data-no-data]').forEach(el => el.remove());
}

function hasData(obj) {
    return Object.values(obj || {}).some(v => v > 0);
}

function showNoData(canvasId, visible) {
    const canvas = document.getElementById(canvasId);
    const parent = canvas.parentElement;
    let msg = parent.querySelector('[data-no-data]');
    if (visible) {
        if (!msg) {
            msg = document.createElement('div');
            msg.setAttribute('data-no-data', 'true');
            msg.className = 'absolute inset-0 flex items-center justify-center text-xs text-gray-400 bg-white/80';
            msg.innerHTML = '<span>No data for selected range</span>';
            parent.classList.add('relative');
            parent.appendChild(msg);
        }
        msg.classList.remove('hidden');
    } else if (msg) {
        msg.classList.add('hidden');
    }
}

function loadReports() {
    const from = document.getElementById('fromDate').value;
    const to = document.getElementById('toDate').value;
    const url = new URL('{{ route('reception.reports.index') }}', window.location.origin);
    if (from) url.searchParams.set('from', from);
    if (to) url.searchParams.set('to', to);

    document.getElementById('reportLoading').classList.remove('hidden');

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.json())
        .then(data => {
            document.getElementById('reportLoading').classList.add('hidden');
            if (!data.success) return;
            renderReport(data);
        })
        .catch(() => {
            document.getElementById('reportLoading').classList.add('hidden');
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load report.', confirmButtonColor: '#024938' });
        });
}

function renderReport(data) {
    document.getElementById('totalVisitors').textContent = data.totals.visitors ?? 0;
    document.getElementById('totalAppointments').textContent = data.totals.appointments ?? 0;
    document.getElementById('totalCalls').textContent = data.totals.calls ?? 0;
    document.getElementById('totalCorrespondence').textContent = data.totals.correspondence ?? 0;
    document.getElementById('totalParcels').textContent = data.totals.parcels ?? 0;
    document.getElementById('totalFrontDesk').textContent = data.totals.front_desk ?? 0;

    destroyCharts();

    const dailyLabels = Object.keys(data.visitorsByDay || {}).map(formatLabel);
    const dailyVisitors = Object.values(data.visitorsByDay || {});
    const dailyAppointments = Object.values(data.appointmentsByDay || {});

    charts.daily = new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [
                { label: 'Visitors', data: dailyVisitors, borderColor: '#024938', backgroundColor: 'rgba(2,73,56,0.1)', tension: 0.3, fill: true },
                { label: 'Appointments', data: dailyAppointments, borderColor: '#0ea5e9', backgroundColor: 'rgba(14,165,233,0.1)', tension: 0.3, fill: true }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });

    const callLabels = Object.keys(data.callsByStatus || {}).map(s => s.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
    const callValues = Object.values(data.callsByStatus || {});
    charts.calls = new Chart(document.getElementById('callsChart'), {
        type: 'doughnut',
        data: {
            labels: callLabels,
            datasets: [{ data: callValues, backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });
    showNoData('callsChart', !hasData(data.callsByStatus));

    const parcelLabels = Object.keys(data.parcelsByStatus || {}).map(s => s.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
    const parcelValues = Object.values(data.parcelsByStatus || {});
    charts.parcels = new Chart(document.getElementById('parcelsChart'), {
        type: 'doughnut',
        data: {
            labels: parcelLabels,
            datasets: [{ data: parcelValues, backgroundColor: ['#f59e0b', '#0ea5e9', '#10b981', '#ef4444'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });
    showNoData('parcelsChart', !hasData(data.parcelsByStatus));

    const corrLabels = Object.keys(data.correspondenceByType || {}).map(t => t.charAt(0).toUpperCase() + t.slice(1));
    const corrValues = Object.values(data.correspondenceByType || {});
    charts.correspondence = new Chart(document.getElementById('correspondenceChart'), {
        type: 'bar',
        data: {
            labels: corrLabels,
            datasets: [{ label: 'Items', data: corrValues, backgroundColor: ['#10b981', '#0ea5e9', '#f59e0b'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
    showNoData('correspondenceChart', !hasData(data.correspondenceByType));

    const fdLabels = Object.keys(data.frontDeskByStatus || {}).map(s => s.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
    const fdValues = Object.values(data.frontDeskByStatus || {});
    charts.frontDesk = new Chart(document.getElementById('frontDeskChart'), {
        type: 'bar',
        data: {
            labels: fdLabels,
            datasets: [{ label: 'Entries', data: fdValues, backgroundColor: ['#f59e0b', '#0ea5e9', '#10b981'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
    showNoData('frontDeskChart', !hasData(data.frontDeskByStatus));
}

loadReports();
</script>
@endpush
@endsection

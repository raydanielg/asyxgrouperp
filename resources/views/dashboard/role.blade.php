@extends('layouts.admin')
@section('title', ucfirst(str_replace('_', ' ', $role)) . ' Dashboard')
@section('page_title', ucfirst(str_replace('_', ' ', $role)) . ' Dashboard')
@section('page_actions')
<a href="{{ route('role.dashboard.report-pdf') }}" onclick="event.preventDefault(); Swal.fire({title:'Generating Report...',text:'Please wait while we prepare your PDF report.',allowOutsideClick:false,allowEscapeKey:false,showConfirmButton:false,willOpen:()=>{Swal.showLoading();fetch('{{ route('role.dashboard.report-pdf') }}').then(r=>r.blob()).then(b=>{const u=URL.createObjectURL(b);const a=document.createElement('a');a.href=u;a.download='role-report-{{ $role }}-{{ now()->format('Ymd') }}.pdf';document.body.appendChild(a);a.click();a.remove();URL.revokeObjectURL(u);Swal.close();}).catch(()=>Swal.fire({icon:'error',title:'Error',text:'Failed to generate PDF',confirmButtonColor:'#024938'}))})" class="px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-xs font-bold rounded-lg hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-sm shadow-emerald-200 inline-flex items-center gap-1.5">
  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
  Export PDF
</a>
@endsection
@section('content')
    @include('dashboard.role-content')
@endsection

@extends('layouts.app')
@section('title', 'Files')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">File Manager</h1>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua file workspace</p>
        </div>
        <!-- Upload button -->
        <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-medium cursor-pointer shadow-lg hover:-translate-y-0.5 transition-all"
               style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Upload File
            <input type="file" class="hidden" multiple onchange="uploadFiles(this)">
        </label>
    </div>

    <!-- Upload zone -->
    <div id="dropZone" class="border-2 border-dashed border-gray-200 dark:border-white/10 rounded-2xl p-10 text-center mb-6 transition hover:border-indigo-400 dark:hover:border-indigo-500/50 cursor-pointer"
         ondrop="handleDrop(event)" ondragover="event.preventDefault()" ondragleave="" ondragenter="">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3" style="background: linear-gradient(135deg, #6366f130, #8b5cf630)">
            <svg class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Drag & drop files here, or <span class="text-indigo-500 font-medium">browse</span></p>
        <p class="text-xs text-gray-400 mt-1">Max 20MB per file</p>
    </div>

    <!-- Files Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-4" id="filesGrid">
        @forelse($files as $file)
        <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-xl p-4 hover:shadow-lg transition group text-center">
            <!-- File icon -->
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3
                {{ str_contains($file->mime_type, 'image') ? 'bg-blue-50 dark:bg-blue-900/20' :
                   (str_contains($file->mime_type, 'pdf') ? 'bg-red-50 dark:bg-red-900/20' :
                   'bg-gray-50 dark:bg-gray-800') }}">
                @if(str_contains($file->mime_type, 'image'))
                    <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                @elseif(str_contains($file->mime_type, 'pdf'))
                    <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                @else
                    <svg class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                @endif
            </div>
            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate mb-1" title="{{ $file->original_name }}">
                {{ $file->original_name }}
            </p>
            <p class="text-xs text-gray-400">{{ $file->formatted_size }}</p>
            <p class="text-xs text-gray-300 dark:text-gray-600 mt-0.5">{{ $file->created_at->diffForHumans() }}</p>
            <a href="{{ $file->url }}" download class="mt-2 text-xs text-indigo-500 hover:underline block">Download</a>
        </div>
        @empty
        <div class="col-span-full text-center py-10 text-gray-400">
            <p>Belum ada file. Upload file pertama Anda!</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $files->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
async function uploadFiles(input) {
    const files = input.files;
    for (const file of files) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', csrfToken);

        try {
            const res = await fetch('/files/upload', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) showToast(`${file.name} berhasil diupload!`, 'success');
        } catch (e) {
            showToast('Upload gagal!', 'error');
        }
    }
    setTimeout(() => location.reload(), 1000);
}

function handleDrop(e) {
    e.preventDefault();
    const files = e.dataTransfer.files;
    const input = document.createElement('input');
    input.type = 'file';
    const dt = new DataTransfer();
    for (const f of files) dt.items.add(f);
    input.files = dt.files;
    uploadFiles(input);
}
</script>
@endpush

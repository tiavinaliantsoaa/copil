@props(['report', 'category', 'panel', 'title' => 'Fichiers', 'description' => '', 'items' => collect(), 'canEdit' => false])

<div class="space-y-3">
    <div>
        <h4 class="text-sm font-bold text-ink">{{ $title }}</h4>
        @if($description)<p class="mt-1 text-xs text-zinc-500">{{ $description }}</p>@endif
    </div>
    @if($canEdit)
        <form method="POST" action="{{ route('attachments.store', $report) }}" enctype="multipart/form-data" class="file-drop block">
            @csrf
            <input type="hidden" name="category" value="{{ $category }}">
            <input type="hidden" name="_panel" value="{{ $panel }}">
            <input type="file" name="file" accept="image/*,.pdf,.ppt,.pptx,.doc,.docx,.xls,.xlsx,.csv" data-file-preview required>
            <div data-preview-output></div>
            <button class="btn btn-secondary mt-3" type="submit"><i data-lucide="upload"></i> Ajouter</button>
        </form>
    @endif
    @if($items->isNotEmpty())
        <div class="preview-grid">
            @foreach($items as $file)
                <article class="preview-item">
                    <a href="{{ route('attachments.show', $file) }}" target="_blank" title="Ouvrir {{ $file->original_name }}">
                        @if($file->isImage())
                            <img src="{{ route('attachments.show', $file) }}" alt="{{ $file->original_name }}">
                        @else
                            <div class="flex aspect-[4/3] items-center justify-center bg-zinc-100 text-zinc-400"><i data-lucide="file-text" class="h-9 w-9"></i></div>
                        @endif
                        <div class="preview-meta">{{ $file->original_name }}</div>
                    </a>
                    @if($canEdit)
                        <div class="preview-actions">
                            <form method="POST" action="{{ route('attachments.destroy', $file) }}" onsubmit="return confirm('Supprimer ce fichier ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-icon" type="submit" title="Supprimer"><i data-lucide="trash-2"></i></button>
                            </form>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    @else
        <p class="text-xs text-zinc-400">Aucun fichier ajouté.</p>
    @endif
</div>

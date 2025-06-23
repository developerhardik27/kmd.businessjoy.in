@forelse ($versionFiles as $major => $subVersions)
    <div class="version-section">
        <div class="main-version">{{ $major }}</div>
        <div class="sub-versions">
            @forelse ($subVersions as $version => $filePath)
                <div class="version-card">
                    <div>{{ $version }}</div>
                    <a href="{{ asset($filePath) }}" target="_blank" download>
                        <button class="btn btn-sm btn-info mt-2" data-toggle="tooltip" data-placement="bottom" title="Download File">
                            <i class="ri-download-line"></i> Download
                        </button>
                    </a>
                </div>
            @empty
                <div class="text-muted">No sub-versions available.</div>
            @endforelse
        </div>
    </div>
@empty
    <div class="text-center text-danger">No version documents found.</div>
@endforelse


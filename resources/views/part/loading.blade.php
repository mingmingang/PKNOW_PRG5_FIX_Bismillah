<div class="loading-spinner">
    @if ($timeout)
        <div class="timeout-message">
            <span>Loading took longer than expected...</span>
        </div>
    @else
        <div class="spinner"></div>
    @endif
</div>

<!-- Include custom styles -->
<link rel="stylesheet" href="{{ asset('style/Loading.css') }}">

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timeoutDuration = {{ $timeout }};
        setTimeout(() => {
            document.querySelector('.loading-spinner').classList.add('timeout');
        }, timeoutDuration);
    });
</script>

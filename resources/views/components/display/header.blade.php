@props(['title', 'description' => null])

<div class="content-bare content-board-header content-title-header">
    <div class="pageTitle barAlt1">{{ $title }}</div>
    @if ($description)
        <div class="pageDescription">{{ $description }}</div>
    @endif
</div>

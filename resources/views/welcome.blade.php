<x-layout>
@foreach($dates as $date)
    <div class="timeremaining" data-unixtime="{{ $date }}"></div>
@endforeach
</x-layout>

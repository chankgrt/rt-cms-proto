<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="choices__inner" x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">

        <select id="user-select" name="author_id" class="choices__input">
            <!-- Options will be populated via JavaScript -->
            <option>sample</option>
        </select>
    </div>
</x-dynamic-component>

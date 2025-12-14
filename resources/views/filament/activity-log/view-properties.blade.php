<div class="space-y-4">
    <div>
        <h3 class="text-lg font-semibold">Activity Details</h3>
    </div>

    <div class="space-y-2">
        <div>
            <span class="font-medium">Log Name:</span>
            <span class="text-gray-700 dark:text-gray-300">{{ $record->log_name ?? 'N/A' }}</span>
        </div>

        <div>
            <span class="font-medium">Description:</span>
            <span class="text-gray-700 dark:text-gray-300">{{ $record->description }}</span>
        </div>

        <div>
            <span class="font-medium">Subject:</span>
            <span class="text-gray-700 dark:text-gray-300">
                {{ $record->subject_type ? class_basename($record->subject_type) : 'N/A' }}
                @if($record->subject_id)
                    (ID: {{ $record->subject_id }})
                @endif
            </span>
        </div>

        <div>
            <span class="font-medium">Causer:</span>
            <span class="text-gray-700 dark:text-gray-300">
                {{ $record->causer?->name ?? 'System' }}
            </span>
        </div>

        <div>
            <span class="font-medium">Event:</span>
            <span class="text-gray-700 dark:text-gray-300">{{ $record->event ?? 'N/A' }}</span>
        </div>

        <div>
            <span class="font-medium">Created At:</span>
            <span class="text-gray-700 dark:text-gray-300">{{ $record->created_at?->format('Y-m-d H:i:s') }}</span>
        </div>
    </div>

    @if($record->properties && $record->properties->isNotEmpty())
        <div class="mt-4">
            <h4 class="font-medium mb-2">Properties:</h4>
            <pre class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg overflow-auto text-sm">{{ json_encode($record->properties, JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif
</div>

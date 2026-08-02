<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('Date')</th>
                <th>@lang('User')</th>
                <th>@lang('Description')</th>
                <th>@lang('Changes')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($activities as $activity)
                <tr>
                    <td>{{ $activity['created_at'] }}</td>
                    <td>{{ $activity['causer']['name'] ?? 'System' }}</td>
                    <td>{{ $activity['description'] }}</td>
                    <td>
                        @forelse ($activity['diffs'] as $field => $diff)
                            <div class="mb-2">
                                <strong>{{ $field }}</strong>
                                <div class="diff-wrapper">{!! $diff !!}</div>
                            </div>
                        @empty
                            <span class="text-muted">@lang('No field diffs captured.')</span>
                        @endforelse
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">@lang('No activity recorded.')</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $activities->links() }}

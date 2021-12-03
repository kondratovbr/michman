Throwable was thrown and wasn't caught in Michman app.



Message:
{!! $exception->getMessage() ?? 'null' !!}

Thrown in:
{!! $exception->getFile() ?? 'null' !!}
Line {!! $exception->getLine() ?? 'null' !!}



Trace:

@forelse($exception->getTrace() as $line)

{!! $line['file'] ?? 'null' !!}
Line {!! $line['line'] ?? 'null' !!}
{!! $line['class'] ?? 'null' !!}
{!! $line['function'] ?? 'null' !!}
@empty

Throwable instance returned empty trace.
@endforelse

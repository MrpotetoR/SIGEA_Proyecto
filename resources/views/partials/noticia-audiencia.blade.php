@php
    /** @var \App\Models\Noticia $noticia */
    $labels = [
        'gestor_escolar' => ['Gestores Escolares', 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800/60'],
        'docente'        => ['Docentes', 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800/60'],
        'alumno'         => ['Alumnos', 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800/60'],
    ];
    $destinatarios = $noticia->destinatarios;
    $esTodos = empty($destinatarios);
@endphp

<div class="mt-2 flex flex-wrap items-center gap-1.5">
    <span class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Dirigida a:</span>
    @if($esTodos)
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium border bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800/60">
            Todos los usuarios
        </span>
    @else
        @foreach($destinatarios as $rol)
            @if(isset($labels[$rol]))
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium border {{ $labels[$rol][1] }}">
                    {{ $labels[$rol][0] }}
                </span>
            @endif
        @endforeach
    @endif
</div>

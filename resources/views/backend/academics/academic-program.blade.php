@extends('backend.layouts.app')

@section('title', __('Academic Program'))

@section('content')

    @php
        use App\Domains\AcademicProgram\AcademicProgram;
        $academicPrograms = AcademicProgram::getVersions();

    @endphp



    <div>
        <x-backend.card>
            <x-slot name="body">
                <div class="container">
                    <h3>Manage Academic Programs</h3>
                    <h4>Undergraduate</h4>
                    <ul>
                        <li>
                            <a
                                href="{{ route('dashboard.semesters.index') }}?filters[academic_program]=undergraduate">Semesters</a>
                            <ul>
                                @foreach ($academicPrograms as $key => $value)
                                    <li>
                                        <a
                                            href="{{ route('dashboard.semesters.index') }}?filters[academic_program]=undergraduate&filters[version]={{ $key }}">{{ $value }}</a>
                                    </li>
                                @endforeach
                            </ul>

                        </li>
                        <li>
                            <a
                                href="{{ route('dashboard.courses.index') }}?filters[academic_program]=undergraduate">Courses</a>
                            <ul>
                                @foreach ($academicPrograms as $key => $value)
                                    <li>
                                        <a
                                            href="{{ route('dashboard.courses.index') }}?filters[academic_program]=undergraduate&filters[version]={{ $key }}">{{ $value }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>

                    <h4>Postgraduate</h4>
                    <ul>
                        <li>
                            <a
                                href="{{ route('dashboard.semesters.index') }}?filters[academic_program]=postgraduate">Semesters</a>
                        </li>
                        <li>
                            <a
                                href="{{ route('dashboard.courses.index') }}?filters[academic_program]=postgraduate">Courses</a>
                        </li>
                    </ul>
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection

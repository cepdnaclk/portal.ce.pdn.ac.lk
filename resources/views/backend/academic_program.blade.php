@extends('backend.layouts.app')

@section('title', __('Manage'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="body">
                <div class="container" x-data="{ hoveredBox: null }">
                    <!-- Semesters Box -->
                    <div class="box"
                        @click="window.location.href='{{ route('dashboard.semesters.index') }}'"
                        @mouseover="hoveredBox = 'semesters'" 
                        @mouseout="hoveredBox = null">
                        <div class="box-content">
                            <b class="title">SEMESTERS</b>
                            <p class="description" x-text="hoveredBox === 'semesters' ? 'View Semesters' : ''"></p>
                        </div>
                    </div>
                    <!-- Courses Box -->
                    <div class="box" 
                        @click="window.location.href='{{ route('dashboard.courses.index') }}'"
                        @mouseover="hoveredBox = 'courses'" 
                        @mouseout="hoveredBox = null">
                        <div class="box-content">
                            <b class="title">COURSES</b>
                            <p class="description" x-text="hoveredBox === 'courses' ? 'View Courses' : ''"></p>
                        </div>
                    </div>
                    
                </div>
            </x-slot>
        </x-backend.card>

        <style>
            body {
                font-family: 'Roboto', sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background-color: #f4f4f9;
            }

            .container {
                display: flex;
                gap: 30px;
                justify-content: center;
            }

            .box {
                border: 2px solid #007bff;
                border-radius: 8px;
                background: linear-gradient(10deg, #3c4b64, #00a0d0); /* Gradient background */
                padding: 20px;
                width: 220px;
                height: 220px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                font-size: 18px;
                cursor: pointer;
                transition: transform 0.3s, box-shadow 0.3s;
                text-align: center;
                font-family: 'Poppins', sans-serif;
            }

            .box:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .box-content {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .title {
                font-size: 24px;
                font-weight: 600; /* Adjusted for a stronger appearance */
                color: #ffffff; /* Adjusted for better contrast on gradient */
                margin-bottom: 10px;
            }

            .description {
                font-size: 16px;
                color: #ffffff; /* Adjusted for better contrast on gradient */
                margin-top: 0;
            }
        </style>
    </div>
@endsection

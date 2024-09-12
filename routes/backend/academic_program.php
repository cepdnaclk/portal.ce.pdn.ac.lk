<?php
use Tabuna\Breadcrumbs\Trail;

Route::get('/academic_program', function () {
  return view('backend.academic_program');
})->name('academic_program.index')
->middleware(['auth', 'permission:user.access.academic']) 
->breadcrumbs(function (Trail $trail) {
    $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Academic Program'), route('dashboard.academic_program.index'));
});
?>

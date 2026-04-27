import AutoAnimate from '@marcreichel/alpine-auto-animate'
import '../../../vendor/rappasoft/laravel-livewire-tables/resources/laravel-livewire-tables.js'

// Register Alpine plugins against the Alpine instance that Livewire provides.
document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(AutoAnimate)
})

window.$ = window.jQuery = require('jquery')
window.Swal = require('sweetalert2')

// CoreUI
require('@coreui/coreui')

// Boilerplate
require('../plugins')

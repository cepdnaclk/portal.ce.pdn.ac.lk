import AutoAnimate from '@marcreichel/alpine-auto-animate'
import Alpine from 'alpinejs'

Alpine.plugin(AutoAnimate)

window.Alpine = Alpine
Alpine.start()

window.$ = window.jQuery = require('jquery')
window.Swal = require('sweetalert2')

// CoreUI
require('@coreui/coreui')

// Boilerplate
require('../plugins')

var elixir = require('laravel-elixir');
require('elixir-react-jsx');


elixir(function(mix) {
    mix
        .jsx('resources/assets/js/doc_appointment/**.js*', 'resources/assets/js/doc_appointment/build')
        .scripts([
            'doc_appointment/build/doctors.js',
        ], 'public/js/doctors.min.js');
});

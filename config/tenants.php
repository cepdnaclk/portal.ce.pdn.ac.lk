<?php

// This can be used to define the tenants for the application at the config level.
// (Can be configured later at the GUI level as well).

return [
  'default' => 'cepdnaclk',

  'tenants' => [
    [
      'slug' => 'cepdnaclk',
      'name' => 'www.ce.pdn.ac.lk',
      'url' => 'https://www.ce.pdn.ac.lk',
      'description' => 'Department of Computer Engineering',
    ],
    [
      'slug' => 'portal',
      'name' => 'portal.ce.pdn.ac.lk',
      'url' => 'https://portal.ce.pdn.ac.lk',
      'description' => 'CE Portal',
    ],
    [
      'slug' => 'pera-swarm',
      'name' => 'pera-swarm.ce.pdn.ac.lk',
      'url' => 'https://pera-swarm.ce.pdn.ac.lk',
      'description' => 'Pera Swarm',
    ],
  ],
];

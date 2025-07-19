<?php

return [
  // TEXT: Context, JsonText, Unified
  // JSON: Combined, Inline, JsonHtml, SideBySide
  'renderer' => 'Combined',


  'calculate_options' => [
    'context' => 3,
    'ignoreCase' => false,
    'ignoreLineEnding' => false,
    'ignoreWhitespace' => false,
    'lengthLimit' => 2000,
    'fullContextIfIdentical' => false,
  ],


  'render_options' => [
    'detailLevel' => 'word',
    'lineNumbers' => true,
    'tabSize' => 4,
    'resultForIdenticals' => null,
  ],
];
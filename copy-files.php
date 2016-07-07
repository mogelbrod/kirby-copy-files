<?php

namespace Kirby\Plugins\CopyFiles;

use Response;
use Dir;

if (!function_exists('panel')) return;

function stripDotSegments($path) {
  return preg_replace('#(^|/)\.{1,}/#', '/', $path);
}

// Load widget
kirby()->set('widget', 'copy-files', __DIR__ . DS . 'widgets' . DS . 'copy-files');

// Add routes
panel()->routes([[
  'pattern' => 'copy-files/api/copy',
  'method' => 'POST',
  'action' => function() {
    $user = site()->user()->current();
    if (!$user || !$user->hasPermission('panel.page.create')) {
      return Response::error("Must be authenticated as user with page creation permissions");
    }

    $sourceUrl = stripDotSegments(get('source'));
    $destUrl = stripDotSegments(get('dest'));

    $source = page($sourceUrl);
    if ($source) {
      $sourceUrl = $source->diruri();
    }

    $sourcePath = kirby()->roots->content() . DS . $sourceUrl;
    $destPath = kirby()->roots->content() . DS . $destUrl;

    if (!file_exists($sourcePath)) {
      return Response::error("Source doesn't exist");
    }
    if (file_exists($destPath)) {
      return Response::error("Destination already exists");
    }
    if (is_dir($sourcePath)) {
      if (!Dir::copy($sourcePath, $destPath)) {
        return Response::error("Failed to copy folder");
      }
    } else if (!@copy($sourcePath, $destPath)) {
      return Response::error("Failed to copy file");
    }

    // Response data
    $data = [];
    if ($source) {
      $data['url'] = panel()->urls->index . "/pages/$destUrl/edit";
      panel()->notify("Page cloned");
    }

    return Response::success("Copy successful", $data);
  },
]]);

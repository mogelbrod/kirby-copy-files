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

    // Convert source uri to its proper path
    $source = page($sourceUrl);
    if ($source) {
      $sourceUrl = $source->diruri();
    }

    // Convert existing page sub-URI of destination to its proper path,
    // adding number prefixes where needed (ie. blog -> 4-blog)
    $destParts = explode('/', $destUrl);
    $destPage = site();
    foreach ($destParts as $index => $part) {
      $destPage = $destPage->children()->find($part);
      if ($destPage == null) {
        break;
      } else {
        $destParts[$index] = $destPage->dirname();
      }
    }

    $sourcePath = kirby()->roots->content() . DS . $sourceUrl;
    $destPath = kirby()->roots->content() . DS . implode('/', $destParts);

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

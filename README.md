# kirby-copy-files
Dashboard widget for Kirby panel that allows users to copy (clone) existing pages and files.

![Screenshot](https://raw.githubusercontent.com/mogelbrod/kirby-copy-files/master/screenshot.png)

Currently only auto-suggests existing pages, not files (but supports file copying as well).

## Installation

### Requirements

-	PHP 5.4.0+
-	Kirby 2.3.0+

### Kirby CLI (untested)

```
cd path/to/kirby
kirby plugin:install mogelbrod/kirby-copy-files
```

This should install the plugin at `site/plugins/copy-files`.

### Git Submodule

```
cd path/to/kirby
git submodule add https://github.com/mogelbrod/kirby-copy-files.git site/plugins/copy-files
```

### Manual download

1. [Download](https://github.com/mogelbrod/kirby-copy-files/archive/master.zip) a ZIP archive of this repository
2. Extract the contents of `master.zip`
3. Rename the extracted folder to `copy-files` and move it into the `site/plugins/` directory in your Kirby project

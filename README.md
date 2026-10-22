KeyMedia for Exponential
========================

KeyMedia is an Exponential extension that connects content to **KeyMedia**, a hosted media
service (digital asset management) for images and video. Editors search, upload and tag media in
KeyMedia from the content edit form, choose a crop for each size the content class defines, and
the site shows the scaled versions straight from KeyMedia's servers. Videos can be played with
Brightcove.

It is for sites that keep their images and video in a KeyMedia account rather than in the content
tree. Without a KeyMedia account (a host, a user name and an API key) the extension has nothing to
connect to.

Contents
--------

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Connecting to KeyMedia](#connecting-to-keymedia)
- [Configuration](#configuration)
- [Usage](#usage)
- [Permissions](#permissions)
- [Upgrading](#upgrading)
- [Known limits](#known-limits)
- [Troubleshooting](#troubleshooting)
- [Developing and contributing](#developing-and-contributing)
- [License](#license)
- [Credits and history](#credits-and-history)

Features
--------

- **The `keymedia` datatype**: a content class attribute that holds one KeyMedia image or video,
  with its alternative text, the crops of each named version and its tags.
- **Named versions per class attribute**: the class defines sizes such as `500x500,main`; editors
  pick the crop of each one (with a focal point scaler), and templates ask for a version by name.
- **Search, upload and tags** from the edit form (through ezjscore calls), against the KeyMedia
  connection the class attribute uses.
- **An Online Editor custom tag**, `keymedia`, to place KeyMedia images inside rich text, with
  versions, CSS classes and view modes chosen from keymedia.ini.
- **The `keymedia` template operator**, which returns the URL and data of a media at a size or a
  named version.
- **The KeyMedia admin tab**, where administrators add and edit the connections to KeyMedia (host,
  user name, API key and API version 1 or 2). In the admin4 design the tab shows the
  collection-play icon before its name.
- Templates for the standard admin and for the ezexceed editing interface.

Requirements
------------

- Exponential 6.x
- PHP 8.0 or later, with the `curl` extension
- The extensions **ezote** (the controller layer the KeyMedia module is built on, Composer package
  `keyteqlabs/ezote`) and **ezjscore** (shipped with Exponential), both active
- Optional: the Online Editor (ezoe) for the custom tag, ezexceed for its editing interface
- MySQL or MariaDB for the connections table (`sql/mysql/install.sql`)
- A KeyMedia account: its host, a user name and an API key

Installation
------------

### With Composer

From the root of the Exponential installation:

```bash
composer require se7enxweb/keymedia-extension
```

The Exponential legacy installer places the package in `extension/keymedia`, and Composer
installs `keyteqlabs/ezote` too.

### By hand

1. Download the latest release from https://github.com/se7enxweb/keymedia-extension/releases (or
   `git clone https://github.com/se7enxweb/keymedia-extension.git`).
2. Copy the folder to `extension/keymedia`. The folder name must be `keymedia`: templates, the
   module and the autoloads refer to that path.
3. Install ezote the same way (https://github.com/KeyteqLabs/ezote) into `extension/ezote`.

### Create the table

```bash
mysql -u <user> -p -h <host> <database> < extension/keymedia/sql/mysql/install.sql
```

This creates `keymedia_backends`, which stores the connections.

### Activate it

Add both extensions to `settings/override/site.ini.append.php` (ezote first):

```ini
[ExtensionSettings]
ActiveExtensions[]=ezote
ActiveExtensions[]=keymedia
```

Then regenerate the autoloads and clear the caches, from the installation root:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

Connecting to KeyMedia
----------------------

1. Sign in to the administration and open the **KeyMedia** tab (`/key_media/dashboard`).
2. Add a connection (`/key_media/connection`): enter the **host**, the **user name**, the
   **API key** and the **API version** (1 or 2) of your KeyMedia account, and save.
3. The dashboard lists the connections; open one to change it (`/key_media/connection/<id>`).

Configuration
-------------

### keymedia.ini

The defaults are in `extension/keymedia/settings/keymedia.ini`. Override them in
`settings/override/keymedia.ini.append.php` or in a siteaccess. Every setting the extension reads:

```ini
<?php /* #?ini charset="utf-8"?

[KeyMedia]
# Width in pixels of the media preview in the edit form.
EditSize=400
# Show a button on the attribute to download the original file (ezexceed interface).
ShowDownload=false
# The id of the connection the Online Editor uses (see the dashboard).
DefaultBackend=1

[Scaler]
# Use the focal point when scaling. false: images without a manual crop are served as the original.
FocalPoint=true

[Brightcove]
# The Brightcove player for videos (example values).
PlayerId=98234987523
PlayerKey=OM932489MGXCV009CXVOM3

[EditorVersion]
# The versions an editor can crop in the Online Editor; at least one.
VersionList[]
VersionList[]=small
VersionList[]=medium
VersionList[]=large
# CSS classes offered in the Online Editor, as class|label.
ClassList[]
ClassList[]=left|Left adjusted
ClassList[]=right|Right adjusted
ClassList[]=center
# View modes offered in the Online Editor, as template|label: the template is
# design/standard/templates/content/datatype/view/ezxmltags/<template>.tpl
ViewModes[]
ViewModes[]=keymedia-standard|Standard view

# One block per version of VersionList: width x height, 0 = unbound (only one side may be 0).
[small]
Size=160x120

[medium]
Size=300x100

[large]
Size=500x0

*/ ?>
```

### Online Editor

To give editors the KeyMedia button, add to `settings/override/ezoe.ini.append.php`:

```ini
[EditorSettings]
Plugins[]=keymedia

[EditorLayout]
Buttons[]=keymedia
```

The extension already registers the plugin and the `keymedia` custom tag (content.ini) with its
attributes; the button can be placed anywhere in `Buttons[]`.

### Other settings the extension adds

`datatype.ini` (the `keymedia` datatype), `content.ini` (the custom tag), `ezjscore.ini` (the
search, media, tag and save calls of the edit form), `module.ini` (the `key_media` module),
`menu.ini` (the KeyMedia tab), `design.ini`, `site.ini` (translations and template autoloads),
`ezexceed.ini` and `datetime.ini`. Nothing there needs changing.

Usage
-----

### 1. Add the attribute to a class

In **Setup → Classes**, edit a class and add an attribute of the datatype **KeyMedia**. Choose the
KeyMedia **connection** and list the **scaled versions**, one per line, as
`<width>x<height>,<name>`:

```
1200x600,wide
500x500,main
160x120,thumbnail
```

### 2. Edit content

In the edit form the attribute offers to search KeyMedia, to upload a file and to tag the media.
After choosing a media, pick the crop of each version and enter the alternative text.

### 3. Show it in a template

The attribute's view template (`content/datatype/view/keymedia.tpl`) renders an image or a video
by the media's type:

```
{attribute_view_gui attribute=$node.data_map.image}
```

With a named version, or a size in pixels:

```
{attribute_view_gui attribute=$node.data_map.image format='main'}
{attribute_view_gui attribute=$node.data_map.image format=array(300, 200)}
```

### 4. The keymedia template operator

`keymedia( <attribute>, <format>, <quality> )` returns the media as a hash with its `url` and
`type`; the format is a version name or `array(width, height)` and defaults to
`array(300, 200)`:

```
{def $media = keymedia( $node.data_map.image, 'main' )}
{if $media.url|is_set}
    <img src="{$media.url}" alt="{$node.name|wash}" />
{/if}

{def $thumb = keymedia( $node.data_map.image, array(160, 120), 80 )}
```

A version name that has not been cropped yet is made from the media's best fit the first time it
is asked for.

### 5. Rich text

With the Online Editor button, editors insert a KeyMedia image into an XML block. The custom tag
stores the media id, the version, the crop, the size, the alternative text, the title, the CSS
class and the view mode, and renders through
`content/datatype/view/ezxmltags/keymedia.tpl`.

### Working with the API in PHP

```php
use keymedia\models\Backend;

$backend = Backend::first( array( 'id' => 1 ) );
// One tag
$results = $backend->tagged( 'a tag' );
// Several tags, all of them
$results = $backend->tagged( array( 'first', 'second' ) );
// Either tag
$results = $backend->tagged( array( 'first', 'second' ), array( 'operator' => 'or' ) );
// At most one result
$results = $backend->tagged( array( 'first', 'second' ), array( 'limit' => 1 ) );
```

`/ezote/delegate/keymedia/user_test/tags` is a small test page that lists the connections and
searches them by tag (its code is in `modules/user_test/UserTest.php`).

Permissions
-----------

The `key_media` module defines no policy functions of its own, so its views (the dashboard, the
connections and the calls of the edit form) are checked against the module as a whole: give the
roles that edit KeyMedia content, and the administrators who manage connections, the policy
**key_media / all functions** in **User accounts → Roles and policies**.

Upgrading
---------

1. Replace the extension's folder with the new release (`composer update se7enxweb/keymedia-extension`,
   or copy the new files over the old ones).
2. Regenerate the autoloads and clear the caches (see [Activate it](#activate-it)).
3. Read [CHANGELOG.md](CHANGELOG.md).

**From the old beta called ezr_keymedia:** rename the folder (`mv extension/ezr_keymedia
extension/keymedia`), run `sql/mysql/upgrade-1.0.sql` (it renames `ezr_keymedia_backends` to
`keymedia_backends`), change `ActiveExtensions[]`, then regenerate the autoloads and clear the
caches.

Known limits
------------

- The extension talks to the KeyMedia service only; it does not store media in the content tree
  and has no offline mode.
- Only MySQL and MariaDB have an install script.
- The code was written for PHP 5.3 and has not been modernized. A few methods declare an optional
  parameter before a required one, which PHP 8 reports as a deprecation notice.
- The unit tests were written for PHPUnit 3.7 and Mockery; they do not run on current PHPUnit
  without changes.
- The module needs ezote, which is no longer developed upstream.

Troubleshooting
---------------

- **The KeyMedia tab is missing or shows "module not found".** Check that both `ezote` and
  `keymedia` are in `ActiveExtensions[]`, regenerate the autoloads and clear the caches.
- **The tab has no icon.** Icons are drawn by the admin4 design; older admin designs show the
  name only.
- **"Table keymedia_backends doesn't exist".** Run `sql/mysql/install.sql`.
- **Search or upload in the edit form does nothing.** Check the connection on the dashboard
  (host, user name, API key, API version) and that the server can reach the KeyMedia host with
  curl; the debug output names the failing call.
- **The Online Editor button is missing.** Add the plugin and the button to ezoe.ini as above, and
  clear the caches.
- **Images ignore the crop.** With `[Scaler] FocalPoint=false` an image without a manual crop is
  served as the original.

Bug reports: https://github.com/se7enxweb/keymedia-extension/issues

Developing and contributing
---------------------------

Pull requests are welcome at https://github.com/se7enxweb/keymedia-extension. The tests live in
`tests/` and need the extension placed in `extension/keymedia` of an installation, because the
models extend `eZPersistentObject`:

```bash
cd extension/keymedia
composer install
phpunit tests/
```

The JavaScript of the edit form is in `design/*/javascript`; `Gruntfile.js` holds the original
build setup.

License
-------

The original authors did not state a license: the repository has no license file, the source
files carry a copyright line only, and the Packagist package `keyteqlabs/keymedia-ezpublish`
declared none. Ask Keyteq AS before reusing the code outside an installation of this extension.

Credits and history
-------------------

Keyteq AS (Keyteq Labs, Norway) wrote KeyMedia between 2011 and 2016 as an extension for
eZ Publish, the content management system that preceded Exponential. It was first called
`ezr_keymedia` and was published on Packagist as `keyteqlabs/keymedia-ezpublish`
(https://github.com/KeyteqLabs/keymedia-extension). Authors named in the code: Raymond Julin,
Kristian Blom and Henning Kvinnesland; later changes came from Bjørnar Helland. 7x maintains this
fork for Exponential as `se7enxweb/keymedia-extension`.

- Copyright (C) 2011 - 2016 Keyteq AS
- Copyright (C) 1998 - 2026 7x

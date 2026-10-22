KeyMedia changelog
==================

The changes of each release, newest first. Earlier releases are described in the history of
the repository (https://github.com/se7enxweb/keymedia-extension/commits/master).

2.3.2 (2026-10-21)
------------------

- Updated: The KeyMedia tab of the top menu shows the collection-play icon in the admin4 design
  (`Icon=collection-play` in [Topmenu_keymedia] of menu.ini.append.php). Older admin designs
  ignore the setting.
- Updated: The package is published as se7enxweb/keymedia-extension. composer.json requires
  PHP 8.0, the curl extension and the Exponential legacy installer next to ezote, links the
  issues and the source, and lists the original authors and 7x as maintainer.
- Added: ezinfo.php, so the about page shows the release, the copyright and the website;
  extension.xml states the same and names ezjscore and the Online Editor as dependencies.
- Updated: The README is rewritten for new users: requirements, installation with Composer and
  by hand, connecting to KeyMedia, every keymedia.ini setting with an example, the datatype,
  the template operator and the custom tag with examples, permissions, upgrading, known limits
  and troubleshooting. The example now puts DefaultBackend in [KeyMedia], where the code reads it.
- Added: This changelog.


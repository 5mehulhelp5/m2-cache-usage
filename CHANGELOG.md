# Changelog

## 3.0.0

### Breaking changes

- Requires Magento 2.4.6 or newer and PHP 8.2 or newer. Older versions of Magento and PHP 7.1 to 8.1 are no longer
  supported; use version 2.x for these.
- Requires `infrangible/m2-task` 3.0, `infrangible/m2-backend-widget` 4.0, `infrangible/m2-core` 5.0,
  `infrangible/m2-foundation` 3.0, `infrangible/m2-simple-mail` 2.0, `fewedev/base` 2.4 and the Magento modules it uses.
- All classes have native types, and the constructors use constructor property promotion. The constructors of the
  observers, plugins and tasks changed; the tasks use the constructor of `Infrangible\Task\Task\Base` 3.0.
- The rows are written by the new `Model\Recorder`, the summary is rendered by the new `Model\Summary`, and
  `Helper\Data` reads the configuration. `Model\Cache` has no `addUncacheableBlock()`, `addCachedBlock()` and
  `addUncachedBlock()` any more, `getBlocksByState()` replaces `getDefaultBlocks()`, `getCachedBlocks()`,
  `getUncachedBlocks()` and `getUncacheableBlocks()`, and durations are `null` if they were not measured.
- The data of a request in the full page cache is stored with the identifier of the page plus `_cache_usage` by a plugin
  of the full page cache type, instead of `_additional`. The kernel plugin is only active in the frontend.
- The tables are declared in `db_schema.xml` instead of the setup script. They have fewer indexes (route, controller and
  action; cacheable and cached; creation time), longer columns for layout paths and templates, and IDs of type `int`.
- The marker of a block shows an "i" instead of an image of an external server: `.block-info-details-icon` is a `span`.

### Fixed

- The setup script could not create the tables on current databases: with utf8mb4 (and on MariaDB 11 also with
  utf8), its indexes over four text columns exceeded the maximal key length of 3072 bytes.
- The IDs of the tables were `smallint` and ran out after 65,535 requests or blocks; then every request failed to store
  its data.
- Missing values (requests that no router matched, for example) and values longer than the column made storing fail with
  an error that broke the page. Values are now stored as empty strings or shortened, and failures are only logged.
- Blocks without a measured render time stored negative durations, which the column does not allow.
- The summary and the layout information were also added to responses that are not HTML pages.
- With Magento 2.4.7 and newer, the data of cached pages was not found, because the full page cache stores pages with
  another identifier than it loads them with.
- Every cached block was loaded twice from the block cache.
- The render time of a block was measured from the generation of the layout instead of the start of its rendering.
- All blocks and layout updates were processed on every request, also with collection disabled.
- "Reduce Cache Usage" compared the creation time in GMT with the local time, and both tasks loaded all IDs of the rows
  to remove into memory.
- The summary showed the durations of uncached, uncacheable and default blocks without unit.
- "Show XML" had the same sort order as "Show Handles".

### Changed

- The summary, the block markers and the layout information are HTML-escaped.

### Added

- Translations in 24 languages and a README.
- Unit tests.

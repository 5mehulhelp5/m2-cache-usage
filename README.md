# Infrangible Cache Usage

Shows how well the full page cache and the block cache of a store work: records for every page request whether the
page was cacheable and served from the full page cache, and for every rendered block whether it was cached, could have
been cached or cannot be cached, with the time it took. The results are reports in the admin, and summaries and
coloured block borders on the storefront while you test.

## Collected data

- **Full page cache:** route, controller, action, path and query parameters of every successful GET or HEAD request
  (and of pages that were not found), whether the page was cacheable and cached, and the duration. With the built-in
  full page cache, the duration is the time the cache needed to load or store the page; otherwise (full page cache
  disabled or Varnish) the time since the start of the request.
- **Blocks:** every block that a page renders, with its path in the layout, class, template file, cache state and render
  time. Pages served from the full page cache render no blocks, so their blocks are not stored again.

| Cache state | Meaning                                                                    | Border |
|-------------|----------------------------------------------------------------------------|--------|
| Uncacheable | declared with `cacheable="false"` in the layout, makes the page uncacheable | red    |
| Cached      | loaded from the block cache                                                | green  |
| Uncached    | has an own cache lifetime, but was not in the block cache                  | yellow |
| Default     | has no own cache, is only cached with the page                             | grey   |

With the built-in full page cache, the route and blocks of a page are stored next to the page in the full page cache,
so that the summary of a cached page shows them as well.

## Admin

- **Infrangible Solutions > Cache Usage > Full Page Cache** and **Block Cache** list the collected data. The rows can be
  grouped by any columns, for example by route and controller, with the number of requests and the average duration.
- **Clear Cache Usage** removes all collected data, **Reduce Cache Usage** the data older than 30 days. The menu entries
  run the tasks; Reduce also runs every hour as cron job `task_cache_usage_reduce` (Stores > Configuration > Tasks >
  Reduce Cache Usage > Execution changes the cron expression).
- **Console:** `bin/magento task:cache_usage_clear` and `bin/magento task:cache_usage_reduce`, with the options of
  [Infrangible Task](https://www.infrangible.dev/modules/task).

## Configuration

Stores > Configuration > Infrangible Solutions > Cache Usage, per website and store view:

- **Full Page Cache:** "Enable Collection of Data" and "Show Summary" below the page.
- **Block Cache:** "Enable Collection of Data", "Show Summary" below the page and the borders of the four cache states
  ("Mark Uncacheable", "Mark Cached", "Mark Uncached", "Mark Default"). Hovering the "i" of a marked block shows its path
  in the layout, class and template.
- **Layout:** "Show Handles" and "Show XML" below the page.

Without collection, the module does nothing on the storefront besides recording the route of the request.

## Good to know

- The summaries, borders and layout information are shown to every visitor of the store view: enable them only on a test
  or staging store, or for a short time.
- Collection writes to the database on every request. It is meant for analysis, not for permanent use in production.
- Summaries are only added to HTML pages, not to AJAX requests or other content.
- For stores with the Hyvä theme, install [Cache Usage Hyvä Theme](https://www.infrangible.dev/modules/cache-usage-hyva)
  as well.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.6 or newer
- PHP 8.2 or newer

## Installation

```bash
composer require infrangible/m2-cache-usage
bin/magento module:enable Infrangible_CacheUsage
bin/magento setup:upgrade
```

In production mode, also run `bin/magento setup:di:compile` and `bin/magento setup:static-content:deploy`.

## License

Infrangible Cache Usage is licensed under the MIT License - see the LICENSE file for details.

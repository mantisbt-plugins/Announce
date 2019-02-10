# Announcements Plugin Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/)
specification.

--------------------------------------------------------------------------------

## [2.4.1] - 2019-02-10

### Added

- Dutch translation
  ([#49](https://github.com/mantisbt-plugins/Announce/issues/49))


## [2.4.0] - 2018-10-10

### Changed

- Hide table header/footer in Announcements list when none are defined
  ([#44](https://github.com/mantisbt-plugins/Announce/issues/44))
- Improve UI for Announcements list actions (2 submit buttons)
  ([#45](https://github.com/mantisbt-plugins/Announce/issues/45))
- Refactor action page into separate scripts (edit/update/delete) 

### Fixed

- SQL Syntax error on PostgreSQL using DELETE statements with JOIN
  ([#40](https://github.com/mantisbt-plugins/Announce/issues/40))
- PHP Notice in Announcements List 
  ([#43](https://github.com/mantisbt-plugins/Announce/issues/43))
- Fix Edit button style and align it to the left 
  ([#46](https://github.com/mantisbt-plugins/Announce/issues/46))


## [2.3.0] - 2018-04-28

### Added

- French translation
  ([#34](https://github.com/mantisbt-plugins/Announce/issues/34))

### Changed

- Improve time-to-live input boxes: numbers >= 0 only, help tooltip
  ([#36](https://github.com/mantisbt-plugins/Announce/issues/36))
- Use specific error messages for invalid TTL or unknown location
  ([#39](https://github.com/mantisbt-plugins/Announce/issues/39))

### Fixed

- Duplicate key error when inserting/updating an already existing context
  ([#35](https://github.com/mantisbt-plugins/Announce/issues/35))
- Consistent display of default in Location selects
  ([#38](https://github.com/mantisbt-plugins/Announce/issues/38))
- Styling of time-to-live input boxes
  ([#36](https://github.com/mantisbt-plugins/Announce/issues/36))


## [2.2.0] - 2018-02-26

### Added

- German translation
  ([#15](https://github.com/mantisbt-plugins/Announce/issues/15))
- Schema update to cleanup orphan records in dismissed table
  [#17](https://github.com/mantisbt-plugins/Announce/issues/17))

### Changed

- Minimum requirement: MantisBT 2.0.0 → 2.3.0
- Use REST API instead of XmlHttpRequest
  ([#22](https://github.com/mantisbt-plugins/snippets/issues/22))
- Improve error handling of announcements dismissals
- Code cleanup
  ([#26](https://github.com/mantisbt-plugins/snippets/issues/26),
  [#27](https://github.com/mantisbt-plugins/snippets/issues/27)...)
- Center '+' button on Edit Announcements page
  ([#28](https://github.com/mantisbt-plugins/snippets/issues/28))
- Show announcements without context as disabled on the list page
  ([#30](https://github.com/mantisbt-plugins/snippets/issues/30))
- Display warning when removing last context
  ([#31](https://github.com/mantisbt-plugins/snippets/issues/31))

### Removed

- Unused language strings

### Fixed

- Setting of Dismissable property when adding a new Context
  ([#18](https://github.com/mantisbt-plugins/Announce/issues/18),
  [#19](https://github.com/mantisbt-plugins/Announce/issues/19))
- Cascade delete Dismissals when removing a Context or Message
  ([#17](https://github.com/mantisbt-plugins/Announce/issues/17))
- Time-to-live for non-dismissable announcements
  ([#16](https://github.com/mantisbt-plugins/Announce/issues/16))
- Messed up display when an announcement has no contexts
  ([#29](https://github.com/mantisbt-plugins/Announce/issues/29))


## [2.1.1] - 2018-02-19

### Fixed

- Display of announcement banner on Admin pages
  ([#25](https://github.com/mantisbt-plugins/Announce/issues/25))


## [2.1.0] - 2017-10-16

### Added

- Implement automatic delayed dismissal
  ([#10](https://github.com/mantisbt-plugins/Announce/issues/10))
- Logging errors on dismissal AJAX to javascript console
  ([#12](https://github.com/mantisbt-plugins/Announce/issues/12))
- Warn admin when configured threshold is lower than '$g_manage_site_threshold'
  ([#14](https://github.com/mantisbt-plugins/Announce/issues/14))

### Changed

- Increase and fix size of Title input field
  ([#13](https://github.com/mantisbt-plugins/Announce/issues/13))

### Fixed

- Markdown rendering in List view
  ([#9](https://github.com/mantisbt-plugins/Announce/issues/9))
- Announcement dismissal not working with detailed *$g_display_error_ settings
  ([#11](https://github.com/mantisbt-plugins/Announce/issues/11))


## [2.0.0] - 2017-07-02

### Added

- Support for MantisBT 2.0
- Screenshots to README file
  ([#4](https://github.com/mantisbt-plugins/Announce/issues/4))

### Removed

- Support for MantisBT 1.3

### Fixed

- Highlight menu item on manage page
  ([#8](https://github.com/mantisbt-plugins/Announce/issues/8))


## [1.0.0] - 2017-06-20

### Added

- Support for MantisBT 1.3

### Removed

- Support for MantisBT 1.2


## [0.3] - 2014-08-12

### Added

- Chinese translation


## [0.2] - 2014-03-19

### Added

- Dismissal timestamps, allowing edited announcements to be shown again,
  until users dismiss them a second time


## [0.1] - 2010-06-19

### Added

- Initial release


[Unreleased]: https://github.com/mantisbt-plugins/Announce/compare/v2.4.1...HEAD

[2.4.1]: https://github.com/mantisbt-plugins/Announce/compare/v2.4.0...v2.1.0
[2.4.0]: https://github.com/mantisbt-plugins/Announce/compare/v2.3.0...v2.4.0
[2.3.0]: https://github.com/mantisbt-plugins/Announce/compare/v2.2.0...v2.3.0
[2.2.0]: https://github.com/mantisbt-plugins/Announce/compare/v2.1.1...v2.2.0
[2.1.1]: https://github.com/mantisbt-plugins/Announce/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/mantisbt-plugins/Announce/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/mantisbt-plugins/Announce/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/mantisbt-plugins/Announce/compare/v0.3...v1.0.0
[0.3]: https://github.com/mantisbt-plugins/Announce/compare/v0.2...v0.3
[0.2]: https://github.com/mantisbt-plugins/Announce/compare/v0.1...v0.2
[0.1]: https://github.com/mantisbt-plugins/Announce/compare/2691884669c6cccf8b51bc1fdc1124d847dbd1d6...v0.1

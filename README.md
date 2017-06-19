# Announcements plugin for MantisBT

Copyright (c) 2010 John Reese - http://noswap.com

Released under the [MIT license](http://opensource.org/licenses/MIT)


## Description

Lets privileged accounts create and post announcements that can be shown to
users on a global or per-project basis, and allow users to dismiss individual
messages.


## Requirements

- MantisBT 1.3.0 or above

If you need compatibility with MantisBT 1.2, please use legacy
[version 0.3](https://github.com/mantisbt-plugins/announce/releases/tag/v0.3).


## Installation

- Copy the whole *Announce* directory under mantisbt/plugins/
- Go to Manage -> Manage Plugins and install the plugin.


## Usage

A new *Announcements* item is added to the Manage menu.

From there, new announcements can be added, targeted at All Projects or a
specific one, restricted by access levels and limited in time.
Existing announcements can be edited and deleted.

At this time, the Announcements can only be displayed at the top of the page
(Location = *Page Header*). In the future, other options may be added.

The *Configuration* page lets the Administrator determine what access levels
are allowed to manage announcements.


## Support

The following support channels are available if you wish to file a
[bug report](https://github.com/mantisbt-plugins/announce/issues/new),
or have questions related to use and installation:

  - [GitHub issues tracker](http://github.com/mantisbt-plugins/announce/issues)
  - MantisBT [Gitter chat room](https://gitter.im/mantisbt/mantisbt)
  - If you feel lucky you may also want to try the legacy
    [#mantisbt IRC channel](https://webchat.freenode.net/?channels=%23mantisbt)
    on Freenode (irc://freenode.net/mantisbt)
    but since hardly anyone goes there nowadays, you may not get any response.


## Change Log

v1.0.0 - 2017-06-20
- MantisBT 1.3 compatibility

v0.3 - 2014-08-12
- Chinese translation

v0.2 - 2014-03-19
- Added dismissal timestamps for announcements, allowing edited ones to be
  shown to users again, until they dismiss them a second time

v0.1 - 2010-06-19
- Initial release


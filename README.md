## Announcements plugin for MantisBT

Copyright (c) 2010 John Reese - http://noswap.com

Released under the [MIT license](http://opensource.org/licenses/MIT)


### Description

Lets privileged accounts create and post announcements that can be shown to
users on a global or per-project basis, and allow users to dismiss individual
messages.


### Requirements

- MantisBT 1.2.0 or above
- [jQuery plugin](https://github.com/mantisbt-plugins/jquery) 1.4 or above


### Installation

- Copy the whole *Announce* directory under mantisbt/plugins/
- If not installed yet, install the jQuery plugin
- Go to Manage -> Manage Plugins and install the plugin.


### Usage

A new *Announcements* item is added to the Manage menu.

From there, new announcements can be added, targeted at All Projects or a
specific one, restricted by access levels and limited in time.
Existing announcements can be edited and deleted.

At this time, the Announcements can only be displayed at the top of the page
(Location = *Page Header*). In the future, other options may be added.

The *Configuration* page lets the Administrator determine what access levels
are allowed to manage announcements.


### Support

Problems or questions dealing with use and installation should be
directed to the [#mantisbt](irc://freenode.net/mantisbt) IRC channel
on Freenode.

The latest source code can found on
[Github](https://github.com/mantisbt-plugins/announce).

We encourage you to submit Bug reports and enhancements requests on the
[Github issues tracker](https://github.com/mantisbt-plugins/announce/issues).
If you would like to propose a patch, do not hesitate to submit a new
[Pull Request](https://github.com/mantisbt-plugins/announce/compare/).


### Change Log

v0.1 - 2010-06-19
- Initial release

v0.2 - 2014-03-19
- Added dismissal timestamps for announcements, allowing edited ones to be
  shown to users again, until they dismiss them a second time

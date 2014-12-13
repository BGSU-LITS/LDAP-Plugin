# LDAP Omeka Plugin

An LDAP plugin for Omeka version 2.2.2+

------

If you are looking for an LDAP plugin that will work with Omeka < 2.0 please
take a look at the plugin developed by [Lehigh University](http://code.google.com/p/omeka-ldap-plugin/).


The Lehigh University plugin was used as the starting point for this plugin so
many thanks to their team!

------

## Installation

Download the plugin by visiting the [downloads](https://github.com/BGSU-LITS/LDAP-Plugin/downloads)
page and selecting the most current version of this plugin. Move those files over
to the plugins directory of your Omeka install.

After the files are moved over you will need to login to Omeka and activate
the plugin.


## Configuration

Since Omeka uses the Zend framework, we can leverage the Zend LDAP Auth Adapter.
Please visit the [Zend documentation page](http://framework.zend.com/manual/1.12/en/zend.auth.adapter.ldap.html#zend.auth.adapter.ldap.server-options)
for more details on each of the options that are configurable within this plugin.

## SSL?

Yes!

Just include the full secure path in the host field (_i.e. ldaps://yourserver.edu_)
and change the port to 636.

## Get Involved

All developement will stay here on Github so please feel free to send pull requests
and add issues to this project. The more the merrier!

------

Developed by the [Bowling Green State University Libraries](http://www.bgsu.edu/colleges/library/index.html)
with help from Lehigh University.

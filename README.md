Earthquake Event Webservice
===========================

An [FDSN](http://www.fdsn.org/webservices/FDSN-WS-Specifications-1.1.pdf)
compatible web service and data feeds for seismic event data from the U.S.
Geological Survey.

Getting Started
---------------

This application supports local installations for a simplified development
environment. In order to set up a local installation of this application you
will require access to an active product index. If you do not have access to an
existing product index, you can [set up a local MySQL product index instance](http://ehppdl1.cr.usgs.gov/).

Once you have access to a product index, you can follow these steps to set up
a local development environemnt.

1. [Use git to clone the earthquake-event-ws from git repository](docs/git.md).
2. [Install needed dependencies](docs/deps.md).
3. Run ```src/lib/pre-install``` from the install directory.
4. Run ```grunt``` from the install directory.
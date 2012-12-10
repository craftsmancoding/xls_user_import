XLS User Import
===============

Import Excel files (.xls) containing user data into MODX Revolution 2.2.x or later.

This is a Custom Manager Page for MODX Revolution that provides a sensible
wizard for importing Excel (.xls) files containing user data.  This can be a great way
to migrate user data from another system (like WordPress or Drupal), or it can be used
to import user data into your MODX-based web application.

Installation:
=============

This package can be installed via standard MODX package management.

Usage:
======

1. Once inside the MODX manager, navigate to Components -> XLS User Importer
2. Select your XLS file and click "Upload"
3. Map each field in your XLS file to a viable MODX user field.  The code takes its best guess as to which fields might be best.
4. Do fine tuning of the mappings by dragging fields into the order you want, e.g. if both "First Name" and "Last Name" are mapped to the MODX "fullname" field, which field should come first?  Should they be separated by a space or by a comma?
5. Optionally, you may edit the names of any extended fields and you may optionally send an email message to each user with a randomly generated password.
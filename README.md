================

RapidPHP is a free, open-source, fast, efficient, and simple object-oriented lightweight PHP development framework.

Version: 4.0.2

Naming Conventions
rapidPHP adheres to the Camel-Case naming convention, the autoload standard, and pays attention to the following rules:

Directories and Files
Directories can be in lowercase or uppercase. For consistency, it is recommended that all directories use lowercase and avoid special symbols like underscores;
Libraries and function files are uniformly suffixed with .php. The first letter of the library name should be uppercase, and the file name should match the library name;
The file names of classes are defined by the namespace, and the path of the namespace is consistent with the path where the library file is located;
The class name and the class file name should be consistent, all named using the Camel-Case convention (with the first letter capitalized).
Function and Class, Attribute Names
The naming of classes uses the Camel-Case, with the first letter capitalized, in the format NameType. For example, BaseController, the suffix Controller can be omitted, but for consistency, it is recommended to include it;
Function names should use lowercase letters and avoid underscores, such as getUser;
Variable names should use Camel-Case, with the first letter in lowercase, for example, tableName, instance;
Functions or methods that begin with a double underscore "__" are used as magic methods, such as __call and __autoload;
Constants and Configuration
All constants are uppercase with underscores;
Configuration parameters can be static methods, constants, or members;
RapidPHP requires PHP7.1+ to run.

Installation
bash

composer require mayi0815/rapid-framework
Contributing to Development
You can contribute by directly submitting a PR or an Issue.

Copyright Information
rapidPHP is released under the MIT open source license and is free to use.

The copyright information for the third-party source code and binary files contained in this project is marked separately.

All rights reserved Copyright © 2020-2024 by rapidPHP.

All rights reserved.
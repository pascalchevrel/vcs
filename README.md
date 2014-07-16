#vcs

PHP library to make it easier to get data from various version control systems used at Mozilla.

Curretly, its only features are:
- Extract commit data from Subversion/Git/Mercurial repositories via the `svn|git|hg log` command and return that data with the same structure (so data extracted from these logs can be more easily merged/compared).
- Run an update command on the repository

###Installable via Composer:
```json
{
    "require": {
        "pascalc/vcs" : "dev-master"
    }
}
```

###Example of use:
```php
<?php
// Import classes in the current namespace
use VCS\Mercurial;
use VCS\Git;
use VCS\Subversion;

// Composer autoloading
require_once __DIR__ . '/vendor/autoload.php';

// Create objects for each repository we want to analyse
$hg  = new Mercurial('/path/to/hg/repo');
$git = new Git('/path/to/git/repo');
$svn = new Subversion('/path/to/svn/repo');

// Dump all the commits extracted
var_dump($hg->getCommits());
var_dump($git->getCommits());
var_dump($svn->getCommits());
```

###Data is returned as a structured array:
```php
[
    0 => [
        ['commit']  => (string) '116254',
        ['author']  => (string) 'Joe Bar',
        ['email']   => (string) 'joe@bar.com',
        ['date']    => (object) DateTime(),
        ['summary'] => (string) 'Commit summary field'
        ['vcs']     => (string) 'hg'
    ],
]
```

The `commit` field contains a subversion revision number (116254), a short mercurial changeset reference (1645:0be17cfdfdb1), or a full git sha1 (dbf6cf2cdc9bf0ddc65e0b9b5fc330a90db6fc40).

The `author` field contains the name of the committer. For Subversion, it is the same value as email as Subversion doesn't have an Author field.

The `email` field is the email used to commit, if the email was not in the log (push by a tool for example), this email is empty.

The `date` is the commit date as a DateTime object with the same formatting for all repositories.

The `summary` is the first line of the commit message.

The `vcs` is the type of Version Control System the commit was done on. The possible values are `svn`, `hg` and `git`.

Note that Subversion being a centralized VCS, it doesn't keep any local history and needs an active internet connection to get the log from the remote server. For that reason it is a good idea to cache that data in case a connection cannot be established and to avoid overloading the remote subversion server.

